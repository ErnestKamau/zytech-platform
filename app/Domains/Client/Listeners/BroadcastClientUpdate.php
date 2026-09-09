<?php

namespace App\Domains\Client\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Client\Events\ClientArchived;
use App\Domains\Client\Events\ClientCreated;
use App\Domains\Client\Events\ClientUpdated;
use App\Domains\Client\Events\CommunicationLogged;
use App\Domains\Client\Events\DocumentUploaded;
use App\Domains\Client\Events\PortalAccessGranted;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class BroadcastClientUpdate extends BaseListener
{
    public string $queue = QueueName::BROADCAST;

    public function handle(
        ClientCreated|ClientUpdated|ClientArchived|DocumentUploaded|CommunicationLogged|PortalAccessGranted $event,
    ): void {
        Log::info('client.broadcast', [
            'event' => class_basename($event),
        ]);
    }
}
