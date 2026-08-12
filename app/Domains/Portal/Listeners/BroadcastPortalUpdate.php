<?php

namespace App\Domains\Portal\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Portal\Events\MeetingCancelled;
use App\Domains\Portal\Events\MeetingScheduled;
use App\Domains\Portal\Events\MessageSent;
use App\Domains\Portal\Events\NotificationCreated;
use App\Domains\Portal\Events\PortalDocumentDownloaded;
use App\Domains\Portal\Events\TicketClosed;
use App\Domains\Portal\Events\TicketOpened;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class BroadcastPortalUpdate extends BaseListener
{
    public string $queue = QueueName::BROADCAST;

    public function handle(
        MessageSent|TicketOpened|TicketClosed|MeetingScheduled|MeetingCancelled|NotificationCreated|PortalDocumentDownloaded $event,
    ): void {
        Log::info('portal.broadcast', ['event' => class_basename($event)]);
    }
}
