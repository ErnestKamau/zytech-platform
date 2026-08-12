<?php

namespace App\Domains\Seo\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final class SeoRedirectPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('settings.view') || $user->can('settings.manage');
    }

    public function view(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('settings.manage');
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can('settings.manage');
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can('settings.manage');
    }
}
