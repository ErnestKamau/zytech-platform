<?php

namespace App\Domains\User\Events;

use App\Core\Events\BusinessEvent;
use App\Models\User;

final class RoleAssigned extends BusinessEvent
{
    /**
     * @param  list<string>  $roles
     */
    public function __construct(
        public User $user,
        public array $roles,
    ) {}
}
