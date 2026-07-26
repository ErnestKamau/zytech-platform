<?php

namespace App\Domains\Authentication\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Authentication\Events\AccountLocked;
use App\Domains\Authentication\Notifications\AccountLockedNotification;
use App\Infrastructure\Queue\QueueName;

final class CreateNotifications extends BaseListener
{
    public string $queue = QueueName::NOTIFICATIONS;

    public function handle(AccountLocked $event): void
    {
        $event->user->notify(new AccountLockedNotification($event->reason));
    }
}
