<?php

namespace App\Domains\Authentication\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Authentication\Events\UserRegistered;
use App\Domains\Authentication\Notifications\WelcomeNotification;
use App\Infrastructure\Queue\QueueName;

final class SendWelcomeEmail extends BaseListener
{
    public string $queue = QueueName::MAIL;

    public function handle(UserRegistered $event): void
    {
        $event->user->notify(new WelcomeNotification);
    }
}
