<?php

namespace App\Domains\Authentication\Livewire;

use App\Core\Enums\UserType;
use App\Core\Livewire\BaseComponent;
use App\Domains\Authentication\Actions\RegisterUser;
use App\Domains\Authentication\Data\RegisterUserData;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.auth')]
#[Title('Create account')]
final class Register extends BaseComponent
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirect(route('account.profile'), navigate: true);
        }
    }

    public function register(RegisterUser $action): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $action->handle(RegisterUserData::fromArray([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'type' => UserType::Client,
        ]));

        event(new Registered($user));
        Auth::login($user);
        session()->regenerate();

        $this->redirect(route('verification.notice'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.authentication.register')
            ->layoutData([
                'asideImageKey' => 'structural_walkway',
                'asideHeadline' => 'Get started with Zyntech.',
                'asideSupport' => 'Complete these easy steps to register your account.',
                'showRegisterSteps' => true,
            ]);
    }
}
