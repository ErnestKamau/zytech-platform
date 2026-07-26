<?php

namespace App\Domains\Authentication\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Authentication\Actions\AuthenticateUser;
use App\Domains\Authentication\Data\LoginData;
use App\Domains\Authentication\Exceptions\AuthenticationFailedException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.auth')]
#[Title('Sign in')]
final class Login extends BaseComponent
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectIntended(route('account.profile'), navigate: true);
        }
    }

    public function login(AuthenticateUser $action): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $action->handle(LoginData::fromArray([
                'email' => $this->email,
                'password' => $this->password,
                'remember' => $this->remember,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]));
        } catch (AuthenticationFailedException $e) {
            $this->addError('email', $e->getMessage());

            return;
        }

        $this->redirectIntended(route('account.profile'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.authentication.login');
    }
}
