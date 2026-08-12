<?php

namespace App\Domains\Client\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Client\Events\ClientCreated;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class NotifyAssignedStaff extends BaseListener
{
    public string $queue = QueueName::NOTIFICATIONS;

    public function handle(ClientCreated $event): void
    {
        Log::info('client.staff.notify', [
            'client_id' => $event->client->getKey(),
            'assigned_sales_id' => $event->client->assigned_sales_id,
        ]);
    }
}
