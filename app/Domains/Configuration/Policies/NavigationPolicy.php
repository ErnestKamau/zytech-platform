<?php

namespace App\Domains\Configuration\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\NavigationMenu;
use App\Models\User;

final class NavigationPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('navigation.view') || $user->can('navigation.manage');
    }

    public function view(User $user, NavigationMenu $menu): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('navigation.manage');
    }

    public function update(User $user, NavigationMenu $menu): bool
    {
        return $user->can('navigation.manage');
    }

    public function delete(User $user, NavigationMenu $menu): bool
    {
        return $user->can('navigation.manage');
    }

    public function publish(User $user, NavigationMenu $menu): bool
    {
        return $user->can('navigation.manage');
    }
}
