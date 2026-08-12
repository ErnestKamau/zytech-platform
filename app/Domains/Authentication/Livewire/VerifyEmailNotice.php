<?php

namespace App\Domains\Authentication\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Authentication\Enums\TwoFactorChannel;
use App\Domains\Authentication\Exceptions\TwoFactorException;
use App\Domains\Authentication\Services\AuthenticationService;
use App\Domains\Authentication\Services\TwoFactorChallengeService;
use App\Domains\Portal\Repositories\PortalRepository;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.auth')]
#[Title('Verify account')]
final class VerifyEmailNotice extends BaseComponent
{
    private const PHONE_REGEX = '/^\+[1-9]\d{7,14}$/';

    public string $channel = '';

    public string $phone = '';

    public string $code = '';

    public bool $codeSent = false;

    public function mount(): void
    {
        $user = Auth::user();

        if ($user === null) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        if ($user->hasVerifiedEmail()) {
            $this->redirect($this->homeRoute(), navigate: true);

            return;
        }

        $this->phone = (string) ($user->phone ?? '');
    }

    public function sendCode(TwoFactorChallengeService $twoFactor): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'channel' => ['required', 'in:email,sms'],
        ]);

        if ($this->channel === TwoFactorChannel::Sms->value) {
            $user = $this->ensurePhoneForSms($user);
        }

        try {
            $twoFactor->issue($user, TwoFactorChannel::from($this->channel), 'verification');
        } catch (TwoFactorException $e) {
            $this->addError('channel', $e->getMessage());

            return;
        }

        $this->codeSent = true;
        session()->flash('status', 'Verification code sent.');
    }

    public function verify(
        TwoFactorChallengeService $twoFactor,
        AuthenticationService $authentication,
    ): void {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'channel' => ['required', 'in:email,sms'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        try {
            $channel = TwoFactorChannel::from($this->channel);
            $twoFactor->verify($user, $channel, $this->code, 'verification');
            $authentication->verifyEmail($user, $channel);
        } catch (TwoFactorException $e) {
            $this->addError('code', $e->getMessage());

            return;
        }

        $this->redirect($this->homeRoute(), navigate: true);
    }

    public function render(): View
    {
        $user = Auth::user();
        $storedPhone = (string) ($user?->phone ?? '');

        return view('livewire.authentication.verify-email', [
            'maskedEmail' => $user?->email,
            'maskedPhone' => $this->maskPhone($storedPhone),
            'needsPhone' => $this->channel === TwoFactorChannel::Sms->value
                && ! $this->isValidPhone($storedPhone),
        ])->layoutData([
            'asideImageKey' => 'commercial_courtyard',
            'asideHeadline' => 'One more step.',
            'asideSupport' => 'Choose email or SMS to verify your account.',
        ]);
    }

    private function ensurePhoneForSms(User $user): User
    {
        if ($this->isValidPhone((string) ($user->phone ?? ''))) {
            return $user;
        }

        $this->validate([
            'phone' => ['required', 'string', 'max:32', 'regex:'.self::PHONE_REGEX],
        ], [
            'phone.required' => 'A phone number is required to receive an SMS code.',
            'phone.regex' => 'Use international format, e.g. +254712345678.',
        ]);

        $user->forceFill(['phone' => $this->phone])->save();

        return $user->fresh() ?? $user;
    }

    private function isValidPhone(string $phone): bool
    {
        return (bool) preg_match(self::PHONE_REGEX, $phone);
    }

    private function homeRoute(): string
    {
        $user = Auth::user();

        if ($user !== null && app(PortalRepository::class)->clientForUser($user) !== null) {
            return route('portal.dashboard');
        }

        return route('account.profile');
    }

    private function maskPhone(string $phone): string
    {
        if ($phone === '' || strlen($phone) < 4) {
            return $phone;
        }

        return str_repeat('•', max(0, strlen($phone) - 4)).substr($phone, -4);
    }
}
