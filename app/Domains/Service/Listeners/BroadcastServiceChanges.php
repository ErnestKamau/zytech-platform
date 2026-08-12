<?php

namespace App\Domains\Service\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Service\Events\FeaturedServiceChanged;
use App\Domains\Service\Events\ServiceArchived;
use App\Domains\Service\Events\ServiceCreated;
use App\Domains\Service\Events\ServicePublished;
use App\Domains\Service\Events\ServiceUpdated;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class BroadcastServiceChanges extends BaseListener
{
    public string $queue = QueueName::BROADCAST;

    public function handle(
        ServiceCreated|ServicePublished|ServiceUpdated|ServiceArchived|FeaturedServiceChanged $event,
    ): void {
        Log::info('service.broadcast', [
            'event' => class_basename($event),
            'service_id' => $event->service->getKey(),
            'slug' => $event->service->slug,
        ]);
    }
}
