<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

final class Login extends BaseLogin
{
    protected static string $layout = 'filament.layouts.admin-auth';

    public const REMEMBER_MINUTES = 60 * 24 * 90;

    public function mount(): void
    {
        parent::mount();

        Auth::guard(filament()->getAuthGuard())->setRememberDuration(self::REMEMBER_MINUTES);

        $email = session()->pull('admin_invite_email');
        $notice = session()->pull('admin_invite_notice');

        if (filled($notice)) {
            session()->flash('status', $notice);
        }

        $this->form->fill([
            'email' => is_string($email) ? $email : ($this->data['email'] ?? ''),
            'remember' => true,
        ]);
    }

    public function getTitle(): string|Htmlable
    {
        return 'Admin sign in';
    }

    public function getHeading(): string|Htmlable|null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getHeading();
        }

        return 'Admin sign in';
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getSubheading();
        }

        if (session('status')) {
            return new HtmlString('<span class="zy-admin-auth__note">'.e((string) session('status')).'</span>');
        }

        return 'Staff console for clients, sales, and projects. You’ll stay signed in for 90 days on this device.';
    }

    public function hasLogo(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                Hidden::make('remember')->default(true),
            ]);
    }

    protected function getRememberFormComponent(): Component
    {
        return Hidden::make('remember')->default(true);
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Sign in to Admin')
            ->submit('authenticate');
    }
}
