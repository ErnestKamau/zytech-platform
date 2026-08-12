<?php

namespace App\Domains\Portal\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Portal\Events\MeetingScheduled;
use App\Domains\Portal\Events\MessageSent;
use App\Domains\Portal\Events\NotificationCreated;
use App\Domains\Portal\Events\TicketOpened;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class SendEmailNotification extends BaseListener
{
    public string $queue = QueueName::MAIL;

    public function handle(MessageSent|TicketOpened|MeetingScheduled|NotificationCreated $event): void
    {
        Log::info('portal.email.notify', ['event' => class_basename($event)]);
    }
}
