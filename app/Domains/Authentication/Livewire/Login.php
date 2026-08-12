<?php

namespace App\Domains\Authentication\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Authentication\Actions\AuthenticateUser;
use App\Domains\Authentication\Data\LoginData;
use App\Domains\Authentication\Enums\LoginStatus;
use App\Domains\Authentication\Exceptions\AuthenticationFailedException;
use App\Domains\Portal\Events\ClientLoggedIn;
use App\Domains\Portal\Repositories\PortalRepository;
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
            $this->redirectIntended($this->homeRoute());
        }
    }

    public function login(AuthenticateUser $action): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $result = $action->handle(LoginData::fromArray([
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

        if ($result->status === LoginStatus::RequiresEmailVerification) {
            $this->redirect(route('verification.notice'));

            return;
        }

        if ($result->status === LoginStatus::RequiresTwoFactor) {
            $this->redirect(route('login.two-factor'));

            return;
        }

        $user = Auth::user();
        if ($user !== null && app(PortalRepository::class)->clientForUser($user) !== null) {
            event(new ClientLoggedIn($user));
        }

        $this->redirectIntended($this->homeRoute());
    }

    public function render(): View
    {
        return view('livewire.authentication.login')
            ->layoutData([
                'asideImageKey' => 'commercial_courtyard',
                'asideHeadline' => 'Sign in to your projects.',
                'asideSupport' => 'Track your build across Nairobi and beyond.',
            ]);
    }

    private function homeRoute(): string
    {
        $user = Auth::user();

        if ($user !== null && app(PortalRepository::class)->clientForUser($user) !== null) {
            return route('portal.dashboard');
        }

        return route('account.profile');
    }
}
