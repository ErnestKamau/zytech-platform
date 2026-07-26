<?php

namespace App\Domains\Authentication\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Authentication\Actions\LogoutUser;
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

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->mfa_enabled = (bool) $user->mfa_enabled;
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

    public function toggleMfaFoundation(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        // Foundation only — stores preference; full TOTP flow comes later.
        $user->forceFill([
            'mfa_enabled' => ! $user->mfa_enabled,
            'mfa_secret' => $user->mfa_enabled ? null : $user->mfa_secret,
        ])->save();

        $this->mfa_enabled = (bool) $user->fresh()->mfa_enabled;
        session()->flash('status', $this->mfa_enabled
            ? 'MFA preference enabled (setup wizard coming soon).'
            : 'MFA preference disabled.');
    }

    public function logout(LogoutUser $action): void
    {
        $action->handle(Auth::user());
        $this->redirect(route('login'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.authentication.security-settings');
    }
}
