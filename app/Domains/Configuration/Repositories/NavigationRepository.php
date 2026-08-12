<?php

namespace App\Domains\Configuration\Repositories;

use App\Core\Enums\NavigationLocation;
use App\Models\NavigationMenu;

final class NavigationRepository
{
    public function publishedFor(NavigationLocation $location): ?NavigationMenu
    {
        return NavigationMenu::query()
            ->where('location', $location)
            ->where('is_published', true)
            ->with(['visibleItems'])
            ->first();
    }

    public function find(string $id): NavigationMenu
    {
        return NavigationMenu::query()->with('items')->findOrFail($id);
    }
}
