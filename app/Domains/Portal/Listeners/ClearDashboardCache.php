<?php

namespace App\Domains\Portal\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Portal\Services\DashboardService;
use App\Models\Client;

final class ClearDashboardCache extends BaseListener
{
    public function __construct(private readonly DashboardService $dashboard) {}

    public function handle(object $event): void
    {
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
            isset($event->quotation) => $event->quotation->client,
            isset($event->order) => $event->order->client,
            isset($event->invoice) => $event->invoice->client,
            isset($event->document) => $event->document->client,
            default => null,
        };
    }
}
