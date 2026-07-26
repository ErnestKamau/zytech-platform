<?php

namespace App\Domains\Authentication\Events;

use App\Core\Events\BusinessEvent;
use App\Models\User;

final class UserLoggedIn extends BusinessEvent
{
    public function __construct(
        public User $user,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
    ) {}
}
