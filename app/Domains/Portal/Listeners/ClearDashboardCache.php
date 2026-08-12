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
use App\Domains\Portal\Services\DashboardService;
use App\Models\Client;

final class ClearDashboardCache extends BaseListener
{
    public function __construct(private readonly DashboardService $dashboard) {}

    public function handle(
        MessageSent|TicketOpened|TicketClosed|MeetingScheduled|MeetingCancelled|NotificationCreated|PortalDocumentDownloaded $event,
    ): void {
        $client = $this->resolveClient($event);

        if ($client !== null) {
            $this->dashboard->forget($client);
        }
    }

    private function resolveClient(object $event): ?Client
    {
        return match (true) {
            isset($event->message) => $event->message->conversation?->client,
            isset($event->ticket) => $event->ticket->client,
            isset($event->meeting) => $event->meeting->client,
            isset($event->notification) => $event->notification->client,
            isset($event->download) => $event->download->client,
            default => null,
        };
    }
}
