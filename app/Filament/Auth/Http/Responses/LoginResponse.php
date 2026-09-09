<?php

namespace App\Filament\Auth\Http\Responses;

use App\Filament\Pages\AdminOnboarding;
use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

final class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = Filament::auth()->user();

        if ($user instanceof User && $user->needsAdminOnboarding()) {
            return redirect()->to(AdminOnboarding::getUrl());
        }

        return redirect()->intended(Filament::getUrl());
    }
}
