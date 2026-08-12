<?php

namespace App\Domains\Client\Services;

use App\Core\Enums\ClientTimelineEvent;
use App\Core\Enums\DocumentVisibility;
use App\Core\Services\BaseService;
use App\Domains\Client\Events\DocumentUploaded;
use App\Models\Client;
use App\Models\ClientDocument;

final class DocumentService extends BaseService
{
    public function __construct(private readonly TimelineService $timeline) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function register(Client $client, array $attributes): ClientDocument
    {
        $document = ClientDocument::query()->create([
            ...$attributes,
            'client_id' => $client->id,
            'uploaded_by' => auth()->id(),
            'visibility' => $attributes['visibility'] ?? DocumentVisibility::Staff,
        ]);

        $this->timeline->record(
            $client,
            ClientTimelineEvent::DocumentUploaded,
            'Document uploaded',
            $document->title,
        );

        event(new DocumentUploaded($document->fresh(['client'])));

        return $document;
    }
}
