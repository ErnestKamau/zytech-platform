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

    public string $phone = '';

    public string $code = '';

    public bool $codeSent = false;

    public function mount(TwoFactorChallengeService $twoFactor): void
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

        if ($this->isValidPhone($this->phone)) {
            $this->dispatchCodes($twoFactor, $user);
        }
    }

    public function sendCode(TwoFactorChallengeService $twoFactor): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $user = $this->ensurePhoneForSms($user);
        $this->dispatchCodes($twoFactor, $user);
    }

    public function verify(
        TwoFactorChallengeService $twoFactor,
        AuthenticationService $authentication,
    ): void {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $channels = $this->deliveryChannels($user);

        try {
            $twoFactor->verifyAny($user, $channels, $this->code, 'verification');
            $authentication->verifyEmail($user, TwoFactorChannel::Email);
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
            'needsPhone' => ! $this->isValidPhone($storedPhone),
        ])->layoutData([
            'asideImageKey' => 'commercial_courtyard',
            'asideHeadline' => 'One more step.',
            'asideSupport' => 'We send the same code to your email and phone.',
        ]);
    }

    private function dispatchCodes(TwoFactorChallengeService $twoFactor, User $user): void
    {
        try {
            $twoFactor->issueToChannels($user, $this->deliveryChannels($user), 'verification');
        } catch (TwoFactorException $e) {
            $this->addError('phone', $e->getMessage());

            return;
        }

        $this->codeSent = true;
        session()->flash('status', 'Verification code sent to email and SMS.');
    }

    /**
     * @return list<TwoFactorChannel>
     */
    private function deliveryChannels(User $user): array
    {
        $channels = [TwoFactorChannel::Email];

        if ($this->isValidPhone((string) ($user->phone ?? ''))) {
            $channels[] = TwoFactorChannel::Sms;
        }

        return $channels;
    }

    private function ensurePhoneForSms(User $user): User
    {
        if ($this->isValidPhone((string) ($user->phone ?? ''))) {
            return $user;
        }

        $this->validate([
            'phone' => ['required', 'string', 'max:32', 'regex:'.self::PHONE_REGEX],
        ], [
            'phone.required' => 'A phone number is required so we can text your code.',
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
