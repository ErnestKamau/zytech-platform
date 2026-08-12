<?php

namespace App\Domains\Client\Services;

use App\Core\Enums\ClientTimelineEvent;
use App\Core\Services\BaseService;
use App\Models\Client;
use App\Models\ClientTimeline;

final class TimelineService extends BaseService
{
    /**
     * @param  array<string, mixed>|null  $meta
     */
    public function record(
        Client $client,
        ClientTimelineEvent $event,
        string $title,
        ?string $description = null,
        ?array $meta = null,
    ): ClientTimeline {
        return ClientTimeline::query()->create([
            'client_id' => $client->id,
            'event_type' => $event,
            'title' => $title,
            'description' => $description,
            'occurred_at' => now(),
            'meta' => $meta,
        ]);
    }
}
