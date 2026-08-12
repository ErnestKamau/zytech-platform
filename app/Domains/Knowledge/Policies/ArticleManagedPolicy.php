<?php

namespace App\Domains\Knowledge\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class ArticleManagedPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('knowledge.view') || $user->can('knowledge.manage');
    }

    public function view(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('knowledge.manage');
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can('knowledge.manage');
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can('knowledge.manage');
    }
}
