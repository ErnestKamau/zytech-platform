<?php

namespace App\Domains\Authentication\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Authentication\Enums\TwoFactorChannel;
use App\Domains\Authentication\Exceptions\TwoFactorException;
use App\Domains\Authentication\Services\AuthenticationService;
use App\Domains\Authentication\Services\TwoFactorChallengeService;
use App\Domains\Portal\Events\ClientLoggedIn;
use App\Domains\Portal\Repositories\PortalRepository;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.auth')]
#[Title('Verify sign-in')]
final class TwoFactorChallenge extends BaseComponent
{
    private const PHONE_REGEX = '/^\+[1-9]\d{7,14}$/';

    public string $code = '';

    public string $phone = '';

    public bool $codeSent = false;

    public function mount(TwoFactorChallengeService $twoFactor): void
    {
        if (Auth::check()) {
            $this->redirectIntended($this->homeRoute());

            return;
        }

        $user = $twoFactor->pendingUser();

        if ($user === null) {
            $this->redirect(route('login'));

            return;
        }

        $channels = $twoFactor->availableChannels($user);

        if ($channels === []) {
            $twoFactor->clearPending();
            $this->redirect(route('login'));

            return;
        }

        $this->phone = (string) ($user->phone ?? '');
        $this->dispatchCodes($twoFactor, $user, $channels);
    }

    public function sendCode(TwoFactorChallengeService $twoFactor): void
    {
        $user = $twoFactor->pendingUser();

        if ($user === null) {
            $this->redirect(route('login'));

            return;
        }

        $channels = $twoFactor->availableChannels($user);

        if ($channels === []) {
            $twoFactor->clearPending();
            $this->redirect(route('login'));

            return;
        }

        if (in_array(TwoFactorChannel::Sms, $channels, true)) {
            $user = $this->ensurePhoneForSms($user);
            $channels = $twoFactor->availableChannels($user);
        }

        $this->dispatchCodes($twoFactor, $user, $channels);
    }

    public function verify(
        TwoFactorChallengeService $twoFactor,
        AuthenticationService $authentication,
    ): void {
        $user = $twoFactor->pendingUser();

        if ($user === null) {
            $this->redirect(route('login'));

            return;
        }

        $this->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $channels = $twoFactor->availableChannels($user);

        try {
            $twoFactor->verifyAny($user, $channels, $this->code);
            $authentication->completeTwoFactorLogin(
                $user,
                $twoFactor->pendingRemember(),
                request()->ip(),
                request()->userAgent(),
            );
        } catch (TwoFactorException $e) {
            $this->addError('code', $e->getMessage());

            return;
        }

        $authenticated = Auth::user();
        if ($authenticated !== null && app(PortalRepository::class)->clientForUser($authenticated) !== null) {
            event(new ClientLoggedIn($authenticated));
        }

        $this->redirectIntended($this->homeRoute());
    }

    public function render(TwoFactorChallengeService $twoFactor): View
    {
        $user = $twoFactor->pendingUser();
        $channels = $user !== null ? $twoFactor->availableChannels($user) : [];
        $storedPhone = (string) ($user?->phone ?? '');

        return view('livewire.authentication.two-factor-challenge', [
            'channels' => $channels,
            'maskedEmail' => $user?->email,
            'maskedPhone' => $this->maskPhone($storedPhone),
            'needsPhone' => in_array(TwoFactorChannel::Sms, $channels, true)
                && ! $this->isValidPhone($storedPhone),
        ])->layoutData([
            'asideImageKey' => 'commercial_courtyard',
            'asideHeadline' => 'Confirm it is you.',
            'asideSupport' => 'We send the same code to your enrolled email and phone.',
        ]);
    }

    /**
     * @param  list<TwoFactorChannel>  $channels
     */
    private function dispatchCodes(TwoFactorChallengeService $twoFactor, User $user, array $channels): void
    {
        try {
            $twoFactor->issueToChannels($user, $channels);
        } catch (TwoFactorException $e) {
            $this->addError('code', $e->getMessage());

            return;
        }

        $this->codeSent = true;
        session()->flash('status', 'Verification code sent.');
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
