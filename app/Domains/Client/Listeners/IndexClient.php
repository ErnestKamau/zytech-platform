<?php

namespace App\Domains\Client\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Client\Events\ClientArchived;
use App\Domains\Client\Events\ClientCreated;
use App\Domains\Client\Events\ClientUpdated;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class IndexClient extends BaseListener
{
    public string $queue = QueueName::SEARCH;

    public function handle(ClientCreated|ClientUpdated|ClientArchived $event): void
    {
        Log::info('client.indexed', [
            'client_id' => $event->client->getKey(),
            'email' => $event->client->email,
        ]);
    }
}
