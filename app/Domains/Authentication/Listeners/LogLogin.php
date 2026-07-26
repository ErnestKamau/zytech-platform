<?php

namespace App\Domains\Authentication\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Authentication\Events\UserLoggedIn;

final class LogLogin extends BaseListener
{
    public function handle(UserLoggedIn $event): void
    {
        activity('authentication')
            ->causedBy($event->user)
            ->performedOn($event->user)
            ->withProperties([
                'ip_address' => $event->ipAddress,
                'user_agent' => $event->userAgent,
            ])
            ->event('login')
            ->log('User logged in');
    }
}
