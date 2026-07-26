<?php

namespace App\Domains\Authentication\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Authentication\Services\AuthenticationService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.auth')]
#[Title('Forgot password')]
final class ForgotPassword extends BaseComponent
{
    public string $email = '';

    public ?string $status = null;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirect(route('account.profile'), navigate: true);
        }
    }

    public function sendResetLink(AuthenticationService $authentication): void
    {
        $this->validate([
            'email' => ['required', 'email'],
        ]);

        $status = $authentication->sendPasswordResetLink($this->email);

        if ($status !== Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->status = __($status);
    }

    public function render(): View
    {
        return view('livewire.authentication.forgot-password');
    }
}
