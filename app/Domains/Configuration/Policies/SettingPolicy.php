<?php

namespace App\Domains\Configuration\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\Setting;
use App\Models\User;

final class SettingPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('settings.view') || $user->can('settings.manage');
    }

    public function view(User $user, Setting $setting): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('settings.manage');
    }

    public function update(User $user, Setting $setting): bool
    {
        return $user->can('settings.manage');
    }

    public function delete(User $user, Setting $setting): bool
    {
        return $user->can('settings.manage');
    }
}
