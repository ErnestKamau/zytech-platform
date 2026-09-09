<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

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

        return 'Admin sign in';
    }

    public function getSubheading(): string | Htmlable | null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getSubheading();
        }

        return 'Staff console for clients, sales, and projects.';
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
