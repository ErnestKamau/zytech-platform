<?php

namespace App\Domains\Service\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Service\Events\ServiceCreated;
use App\Domains\Service\Events\ServicePublished;
use App\Domains\Service\Events\ServiceUpdated;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class IndexService extends BaseListener
{
    public string $queue = QueueName::SEARCH;

    public function handle(ServiceCreated|ServicePublished|ServiceUpdated $event): void
    {
        Log::info('service.indexed', [
            'service_id' => $event->service->getKey(),
            'slug' => $event->service->slug,
        ]);
    }
}
