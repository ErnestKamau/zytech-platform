<?php

namespace App\Domains\Operations\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final class DomainActivityPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('clients.view') || $user->can('commerce.view');
    }

    public function view(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Model $model): bool
    {
        return false;
    }

    public function delete(User $user, Model $model): bool
    {
        return false;
    }
}
