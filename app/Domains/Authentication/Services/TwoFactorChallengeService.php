<?php

namespace App\Domains\Authentication\Services;

use App\Core\Services\BaseService;
use App\Domains\Authentication\Enums\TwoFactorChannel;
use App\Domains\Authentication\Exceptions\TwoFactorException;
use App\Domains\Communication\Mail\AuthOtpMail;
use App\Domains\Communication\Services\TwilioSmsService;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use RuntimeException;
use Throwable;

final class TwoFactorChallengeService extends BaseService
{
    public const CODE_TTL_SECONDS = 600;

    public const MAX_VERIFY_ATTEMPTS = 5;

    public const RESEND_DECAY_SECONDS = 60;

    public const SESSION_USER_ID = 'login.id';

    public const SESSION_REMEMBER = 'login.remember';

    public function __construct(
        private readonly TwilioSmsService $sms,
    ) {}

    public function startPending(User $user, bool $remember): void
    {
        session([
            self::SESSION_USER_ID => $user->id,
            self::SESSION_REMEMBER => $remember,
        ]);
    }

    public function clearPending(): void
    {
        session()->forget([self::SESSION_USER_ID, self::SESSION_REMEMBER]);
    }

    public function pendingUser(): ?User
    {
        $id = session(self::SESSION_USER_ID);

        if (! is_string($id) || $id === '') {
            return null;
        }

        return User::query()->find($id);
    }

    public function pendingRemember(): bool
    {
        return (bool) session(self::SESSION_REMEMBER, false);
    }

    /**
     * @return list<TwoFactorChannel>
     */
    public function availableChannels(User $user): array
    {
        $channels = [];

        if ($user->mfaEmailEnabled()) {
            $channels[] = TwoFactorChannel::Email;
        }

        if ($user->mfaSmsEnabled() && filled($user->phone)) {
            $channels[] = TwoFactorChannel::Sms;
        }

        return $channels;
    }

    public function issue(User $user, TwoFactorChannel $channel, string $purpose = 'login'): void
    {
        $this->issueToChannels($user, [$channel], $purpose);
    }

    /**
     * Issue one shared code to every given channel (email and/or SMS).
     *
     * @param  list<TwoFactorChannel>  $channels
     */
    public function issueToChannels(User $user, array $channels, string $purpose = 'login'): void
    {
        $channels = array_values(array_unique($channels, SORT_REGULAR));

        if ($channels === []) {
            throw TwoFactorException::channelUnavailable();
        }

        foreach ($channels as $channel) {
            if ($purpose === 'login' && ! in_array($channel, $this->availableChannels($user), true)) {
                throw TwoFactorException::channelUnavailable();
            }

            if (
                in_array($purpose, ['enrollment', 'verification'], true)
                && $channel === TwoFactorChannel::Sms
                && ! filled($user->phone)
            ) {
                throw TwoFactorException::channelUnavailable();
            }
        }

        $resendKey = $this->bundleResendKey($user->id, $purpose);

        if (RateLimiter::tooManyAttempts($resendKey, 1)) {
            throw TwoFactorException::resendThrottled(RateLimiter::availableIn($resendKey));
        }

        $code = (string) random_int(100000, 999999);
        $payload = [
            'hash' => Hash::make($code),
            'attempts' => 0,
        ];

        foreach ($channels as $channel) {
            Cache::put(
                $this->cacheKey($user->id, $channel, $purpose),
                $payload,
                now()->addSeconds(self::CODE_TTL_SECONDS),
            );
        }

        $failures = [];

        foreach ($channels as $channel) {
            try {
                match ($channel) {
                    TwoFactorChannel::Email => $this->sendEmailCode($user, $code, $purpose),
                    TwoFactorChannel::Sms => $this->sendSmsCode($user, $code, $purpose),
                };
            } catch (Throwable $exception) {
                $failures[] = $channel === TwoFactorChannel::Sms
                    ? 'SMS could not be sent. Check Twilio configuration and your phone number.'
                        .($exception instanceof RuntimeException ? ' '.$exception->getMessage() : '')
                    : 'Email could not be sent. Please try again shortly.';
            }
        }

        if (count($failures) === count($channels)) {
            foreach ($channels as $channel) {
                Cache::forget($this->cacheKey($user->id, $channel, $purpose));
            }

            throw TwoFactorException::sendFailed(implode(' ', $failures));
        }

        RateLimiter::hit($resendKey, self::RESEND_DECAY_SECONDS);
    }

    public function verify(User $user, TwoFactorChannel $channel, string $code, string $purpose = 'login'): void
    {
        $this->verifyAny($user, [$channel], $code, $purpose);
    }

    /**
     * Accept a code that was issued to any of the given channels (same shared OTP).
     *
     * @param  list<TwoFactorChannel>  $channels
     */
    public function verifyAny(User $user, array $channels, string $code, string $purpose = 'login'): void
    {
        $channels = array_values(array_unique($channels, SORT_REGULAR));
        $code = trim($code);
        $keys = [];
        $hash = null;
        $attempts = 0;

        foreach ($channels as $channel) {
            $key = $this->cacheKey($user->id, $channel, $purpose);
            $payload = Cache::get($key);

            if (! is_array($payload) || ! isset($payload['hash'])) {
                continue;
            }

            $keys[] = $key;
            $hash = (string) $payload['hash'];
            $attempts = max($attempts, (int) ($payload['attempts'] ?? 0));
        }

        if ($hash === null || $keys === []) {
            throw TwoFactorException::invalidCode();
        }

        if ($attempts >= self::MAX_VERIFY_ATTEMPTS) {
            foreach ($keys as $key) {
                Cache::forget($key);
            }

            throw TwoFactorException::tooManyAttempts(self::RESEND_DECAY_SECONDS);
        }

        if (! Hash::check($code, $hash)) {
            $attempts++;
            $payload = [
                'hash' => $hash,
                'attempts' => $attempts,
            ];

            foreach ($keys as $key) {
                Cache::put($key, $payload, now()->addSeconds(self::CODE_TTL_SECONDS));
            }

            if ($attempts >= self::MAX_VERIFY_ATTEMPTS) {
                foreach ($keys as $key) {
                    Cache::forget($key);
                }

                throw TwoFactorException::tooManyAttempts(self::RESEND_DECAY_SECONDS);
            }

            throw TwoFactorException::invalidCode();
        }

        foreach ($channels as $channel) {
            Cache::forget($this->cacheKey($user->id, $channel, $purpose));
            RateLimiter::clear($this->resendKey($user->id, $channel, $purpose));
        }

        RateLimiter::clear($this->bundleResendKey($user->id, $purpose));
    }

    private function sendEmailCode(User $user, string $code, string $purpose): void
    {
        Mail::mailer(config('mail.default'))->to($user->email)->send(new AuthOtpMail(
            purpose: $purpose,
            code: $code,
            userName: (string) ($user->name ?? 'there'),
        ));
    }

    private function sendSmsCode(User $user, string $code, string $purpose): void
    {
        $phone = (string) $user->phone;

        if ($phone === '') {
            throw new RuntimeException('Phone number is required for SMS OTP.');
        }

        $prefix = match ($purpose) {
            'enrollment' => 'Zytech 2FA setup code',
            'verification' => 'Zytech verification code',
            default => 'Zytech sign-in code',
        };

        $this->sms->send($phone, "{$prefix}: {$code}. Expires in 10 minutes.");
    }

    private function cacheKey(string $userId, TwoFactorChannel $channel, string $purpose): string
    {
        return "auth.otp.{$purpose}.{$userId}.{$channel->value}";
    }

    private function resendKey(string $userId, TwoFactorChannel $channel, string $purpose): string
    {
        return "auth.otp.resend.{$purpose}.{$userId}.{$channel->value}";
    }

    private function bundleResendKey(string $userId, string $purpose): string
    {
        return "auth.otp.resend.{$purpose}.{$userId}.bundle";
    }
}
