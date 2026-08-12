<?php

namespace App\Domains\Portal\Events;

use App\Core\Events\BusinessEvent;
use App\Models\User;

final class ClientLoggedIn extends BusinessEvent
{
    public function __construct(public User $user) {}
}
