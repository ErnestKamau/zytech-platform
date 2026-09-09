<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Permissions\PermissionResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Sessions\SessionResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Support\Icons\Heroicon;

class IdentityHub extends AdminHubPage
{
    protected static ?string $navigationLabel = 'Identity';

    protected static ?string $title = 'Identity';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?int $navigationSort = 12;

    protected function getHubSubheading(): ?string
    {
        return 'Users, roles, permissions, and sessions.';
    }

    public function getHubSections(): array
    {
        return [
            [
                'title' => 'Access',
                'cards' => [
                    $this->hubCard('Users', UserResource::class, Heroicon::OutlinedUsers, count: User::query()->count()),
                    $this->hubCard('Roles', RoleResource::class, Heroicon::OutlinedShieldCheck),
                    $this->hubCard('Permissions', PermissionResource::class, Heroicon::OutlinedKey),
                    $this->hubCard('Sessions', SessionResource::class, Heroicon::OutlinedComputerDesktop),
                ],
            ],
        ];
    }
}
