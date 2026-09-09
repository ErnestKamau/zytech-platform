<?php

namespace App\Filament\Pages;

use App\Domains\Authentication\Services\StaffInviteService;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;

final class AdminOnboarding extends Page
{
    protected static string $layout = 'filament.layouts.admin-auth';

    protected string $view = 'filament.pages.admin-onboarding';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'onboarding';

    protected static ?string $title = 'Welcome to Admin';

    #[Locked]
    public int $step = 1;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->canAccessPanel(Filament::getCurrentOrDefaultPanel())
            && $user->needsAdminOnboarding();
    }

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user instanceof User || ! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel())) {
            $this->redirect(Filament::getLoginUrl() ?? url('/admin/login'));

            return;
        }

        if (! $user->needsAdminOnboarding()) {
            $this->redirect(Filament::getUrl());
        }
    }

    public function getTitle(): string|Htmlable
    {
        return 'Welcome to Admin';
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function nextStep(): void
    {
        $this->step = min(2, $this->step + 1);
    }

    public function previousStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function finish(string $destination, StaffInviteService $invites): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $invites->completeOnboarding($user);

        if ($destination === 'website') {
            $this->redirect(url('/'));

            return;
        }

        $this->redirect(Filament::getUrl());
    }
}
