<?php

namespace App\Domains\Authentication\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Authentication\Events\UserLoggedOut;

final class LogLogout extends BaseListener
{
    public function handle(UserLoggedOut $event): void
    {
        activity('authentication')
            ->causedBy($event->user)
            ->performedOn($event->user)
            ->event('logout')
            ->log('User logged out');
    }
}
