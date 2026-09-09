<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

final class Login extends BaseLogin
{
    protected static string $layout = 'filament.layouts.admin-auth';

    public function getTitle(): string | Htmlable
    {
        return 'Admin sign in';
    }

    public function getHeading(): string | Htmlable | null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getHeading();
        }

        return 'Operations console';
    }

    public function getSubheading(): string | Htmlable | null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getSubheading();
        }

        return new HtmlString(
            'Sign in to manage clients, quotations, orders, projects, and the Zytech team. '
            .'<span class="zy-admin-auth__note">Staff access only — not the client portal.</span>'
        );
    }

    public function hasLogo(): bool
    {
        return false;
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Sign in to Admin')
            ->submit('authenticate');
    }
}
