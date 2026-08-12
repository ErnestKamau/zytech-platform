<?php

namespace App\Domains\Authentication\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Authentication\Actions\ResetPassword as ResetPasswordAction;
use App\Domains\Authentication\Data\ResetPasswordData;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;

#[Layout('layouts.auth')]
#[Title('Reset password')]
final class ResetPassword extends BaseComponent
{
    #[Locked]
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        if (Auth::check()) {
            $this->redirect(route('account.profile'), navigate: true);
        }

        $this->token = $token;
        $this->email = (string) request()->query('email', '');
    }

    public function resetPassword(ResetPasswordAction $action): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = $action->handle(ResetPasswordData::fromArray([
            'email' => $this->email,
            'token' => $this->token,
            'password' => $this->password,
        ]));

        if ($status !== Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        session()->flash('status', __($status));
        $this->redirect(route('login'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.authentication.reset-password')
            ->layoutData([
                'asideImageKey' => 'paving_gravel_leveling',
                'asideHeadline' => 'Choose a new password.',
                'asideSupport' => 'Then sign in and pick up where you left off.',
            ]);
    }
}
