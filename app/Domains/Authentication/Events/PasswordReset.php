<?php

namespace App\Domains\Authentication\Events;

use App\Core\Events\BusinessEvent;
use App\Models\User;

final class PasswordReset extends BusinessEvent
{
    public function __construct(public User $user) {}
}
