<?php

namespace App\Domains\Client\Services;

use App\Core\Enums\ClientTimelineEvent;
use App\Core\Enums\CommunicationMethod;
use App\Core\Services\BaseService;
use App\Domains\Client\Events\CommunicationLogged;
use App\Models\Client;
use App\Models\ClientCommunication;

final class CommunicationService extends BaseService
{
    public function __construct(private readonly TimelineService $timeline) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function log(Client $client, array $attributes): ClientCommunication
    {
        $communication = ClientCommunication::query()->create([
            ...$attributes,
            'client_id' => $client->id,
            'occurred_at' => $attributes['occurred_at'] ?? now(),
            'logged_by' => auth()->id(),
        ]);

        $channel = $communication->channel instanceof CommunicationMethod
            ? $communication->channel
            : CommunicationMethod::from((string) $communication->channel);

        $this->timeline->record(
            $client,
            ClientTimelineEvent::CommunicationLogged,
            $communication->subject ?? $channel->label(),
            $communication->summary,
        );

        event(new CommunicationLogged($communication->fresh(['client'])));

        return $communication;
    }
}
