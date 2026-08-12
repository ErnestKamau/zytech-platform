<?php

namespace App\Domains\Authentication\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Authentication\Enums\TwoFactorChannel;
use App\Domains\Authentication\Exceptions\TwoFactorException;
use App\Domains\Authentication\Services\TwoFactorChallengeService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.portal')]
#[Title('Security')]
final class SecuritySettings extends BaseComponent
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $mfa_enabled = false;

    public bool $mfa_email_enabled = true;

    public bool $mfa_sms_enabled = false;

    public string $phone = '';

    public string $enrollment_channel = 'email';

    public string $enrollment_code = '';

    public bool $awaitingEnrollmentCode = false;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->mfa_enabled = (bool) $user->mfa_enabled;
        $this->mfa_email_enabled = $user->mfaEmailEnabled();
        $this->mfa_sms_enabled = $user->mfaSmsEnabled();
        $this->phone = (string) ($user->phone ?? '');
    }

    public function updatePassword(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($this->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $user->forceFill(['password' => $this->password])->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('status', 'Password updated.');
    }

    public function savePhone(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'phone' => ['required', 'string', 'max:32', 'regex:/^\+[1-9]\d{7,14}$/'],
        ], [
            'phone.regex' => 'Use international format, e.g. +254712345678.',
        ]);

        $user->forceFill(['phone' => $this->phone])->save();
        session()->flash('status', 'Phone number saved.');
    }

    public function beginEnableTwoFactor(TwoFactorChallengeService $twoFactor): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'mfa_email_enabled' => ['boolean'],
            'mfa_sms_enabled' => ['boolean'],
            'enrollment_channel' => ['required', 'in:email,sms'],
        ]);

        if (! $this->mfa_email_enabled && ! $this->mfa_sms_enabled) {
            throw ValidationException::withMessages([
                'mfa_email_enabled' => 'Enable at least one method: email or SMS.',
            ]);
        }

        if ($this->mfa_sms_enabled && ! filled($user->phone) && ! filled($this->phone)) {
            throw ValidationException::withMessages([
                'phone' => 'Save a phone number in +E.164 format before enabling SMS OTP.',
            ]);
        }

        if ($this->enrollment_channel === 'sms' && ! $this->mfa_sms_enabled) {
            throw ValidationException::withMessages([
                'enrollment_channel' => 'Select SMS as an enabled method first.',
            ]);
        }

        if ($this->enrollment_channel === 'email' && ! $this->mfa_email_enabled) {
            throw ValidationException::withMessages([
                'enrollment_channel' => 'Select email as an enabled method first.',
            ]);
        }

        if (filled($this->phone) && $this->phone !== (string) $user->phone) {
            $this->validate([
                'phone' => ['required', 'string', 'max:32', 'regex:/^\+[1-9]\d{7,14}$/'],
            ], [
                'phone.regex' => 'Use international format, e.g. +254712345678.',
            ]);
            $user->forceFill(['phone' => $this->phone])->save();
        }

        $user->setMfaPreferences([
            'mfa_email_enabled' => $this->mfa_email_enabled,
            'mfa_sms_enabled' => $this->mfa_sms_enabled,
        ]);
        $user->save();

        try {
            $twoFactor->issue(
                $user->fresh(),
                TwoFactorChannel::from($this->enrollment_channel),
                'enrollment',
            );
        } catch (TwoFactorException $e) {
            $this->addError('enrollment_channel', $e->getMessage());

            return;
        }

        $this->awaitingEnrollmentCode = true;
        session()->flash('status', 'Confirmation code sent. Enter it below to finish enabling 2FA.');
    }

    public function confirmEnableTwoFactor(TwoFactorChallengeService $twoFactor): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'enrollment_channel' => ['required', 'in:email,sms'],
            'enrollment_code' => ['required', 'string', 'size:6'],
        ]);

        try {
            $twoFactor->verify(
                $user,
                TwoFactorChannel::from($this->enrollment_channel),
                $this->enrollment_code,
                'enrollment',
            );
        } catch (TwoFactorException $e) {
            $this->addError('enrollment_code', $e->getMessage());

            return;
        }

        $user->forceFill([
            'mfa_enabled' => true,
            'mfa_secret' => null,
        ])->save();

        $user->setMfaPreferences([
            'mfa_email_enabled' => $this->mfa_email_enabled,
            'mfa_sms_enabled' => $this->mfa_sms_enabled,
        ]);
        $user->save();

        $this->mfa_enabled = true;
        $this->awaitingEnrollmentCode = false;
        $this->enrollment_code = '';
        session()->flash('status', 'Two-factor authentication is now enabled.');
    }

    public function disableTwoFactor(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $user->forceFill([
            'mfa_enabled' => false,
            'mfa_secret' => null,
        ])->save();

        $this->mfa_enabled = false;
        $this->awaitingEnrollmentCode = false;
        session()->flash('status', 'Two-factor authentication disabled.');
    }

    public function render(): View
    {
        return view('livewire.authentication.security-settings');
    }
}
