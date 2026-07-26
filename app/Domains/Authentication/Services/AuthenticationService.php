<?php

namespace App\Domains\Authentication\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Services\BaseService;
use App\Domains\Authentication\Data\LoginData;
use App\Domains\Authentication\Data\RegisterUserData;
use App\Domains\Authentication\Data\ResetPasswordData;
use App\Domains\Authentication\Events\AccountLocked;
use App\Domains\Authentication\Events\EmailVerified;
use App\Domains\Authentication\Events\PasswordReset;
use App\Domains\Authentication\Events\UserLoggedIn;
use App\Domains\Authentication\Events\UserLoggedOut;
use App\Domains\Authentication\Exceptions\AuthenticationFailedException;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset as LaravelPasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

final class AuthenticationService extends BaseService
{
    public const MAX_ATTEMPTS = 5;

    public const LOCK_AFTER_FAILURES = 5;

    public const DECAY_SECONDS = 60;

    public function __construct(
        private readonly CacheStore $cache,
        private readonly RegistrationService $registration,
    ) {}

    public function register(RegisterUserData $data): User
    {
        return $this->registration->register($data);
    }

    public function authenticate(LoginData $data): User
    {
        $this->ensureIsNotRateLimited($data);

        $user = User::query()->where('email', (string) $data->email)->first();

        if ($user?->isLocked()) {
            throw AuthenticationFailedException::accountLocked($user->lock_reason);
        }

        if (! Auth::attempt(
            ['email' => (string) $data->email, 'password' => $data->password],
            $data->remember,
        )) {
            if ($user !== null) {
                $this->recordFailedAttempt($user);
            }

            RateLimiter::hit($this->throttleKey($data), self::DECAY_SECONDS);

            throw AuthenticationFailedException::invalidCredentials();
        }

        /** @var User $authenticated */
        $authenticated = Auth::user();

        $authenticated->forceFill([
            'failed_login_attempts' => 0,
            'locked_at' => null,
            'lock_reason' => null,
        ])->save();

        RateLimiter::clear($this->throttleKey($data));
        session()->regenerate();

        event(new UserLoggedIn(
            user: $authenticated,
            ipAddress: $data->ipAddress,
            userAgent: $data->userAgent,
        ));

        return $authenticated;
    }

    public function logout(?User $user = null): void
    {
        $user ??= Auth::user();

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        if ($user instanceof User) {
            event(new UserLoggedOut($user));
        }
    }

    public function sendPasswordResetLink(string $email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }

    public function resetPassword(ResetPasswordData $data): string
    {
        $status = Password::reset(
            [
                'email' => (string) $data->email,
                'password' => $data->password,
                'password_confirmation' => $data->password,
                'token' => $data->token,
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                    'failed_login_attempts' => 0,
                    'locked_at' => null,
                    'lock_reason' => null,
                ])->save();

                event(new LaravelPasswordReset($user));
                event(new PasswordReset($user));
            },
        );

        return $status;
    }

    public function verifyEmail(User $user): User
    {
        if ($user->hasVerifiedEmail()) {
            return $user;
        }

        $user->markEmailAsVerified();
        event(new EmailVerified($user));

        return $user->refresh();
    }

    public function lockAccount(User $user, string $reason = 'Too many failed login attempts'): User
    {
        $user->forceFill([
            'locked_at' => now(),
            'lock_reason' => $reason,
        ])->save();

        event(new AccountLocked($user, $reason));

        return $user->refresh();
    }

    public function unlockAccount(User $user): User
    {
        $user->forceFill([
            'locked_at' => null,
            'lock_reason' => null,
            'failed_login_attempts' => 0,
        ])->save();

        $this->cache->forget($this->permissionCacheKey($user->id));

        return $user->refresh();
    }

    private function recordFailedAttempt(User $user): void
    {
        $attempts = $user->failed_login_attempts + 1;

        $user->forceFill(['failed_login_attempts' => $attempts])->save();

        if ($attempts >= self::LOCK_AFTER_FAILURES) {
            $this->lockAccount($user);
        }
    }

    private function ensureIsNotRateLimited(LoginData $data): void
    {
        $key = $this->throttleKey($data);

        if (! RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            return;
        }

        throw AuthenticationFailedException::tooManyAttempts(
            RateLimiter::availableIn($key),
        );
    }

    private function throttleKey(LoginData $data): string
    {
        return Str::lower((string) $data->email).'|'.($data->ipAddress ?? 'cli');
    }

    private function permissionCacheKey(string $userId): string
    {
        return "user.{$userId}.permissions";
    }
}
