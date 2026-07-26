<?php

namespace App\Domains\User\Events;

use App\Core\Events\BusinessEvent;
use App\Models\User;

final class PermissionAssigned extends BusinessEvent
{
    /**
     * @param  list<string>  $permissions
     */
    public function __construct(
        public User $user,
        public array $permissions,
    ) {}
}
