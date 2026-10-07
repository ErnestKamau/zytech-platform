<?php

namespace App\Domains\Communication\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final class NotificationLogPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('settings.manage') || $user->can('clients.manage');
    }

    public function view(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }
}
