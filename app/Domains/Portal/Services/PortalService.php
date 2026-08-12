<?php

namespace App\Domains\Portal\Services;

use App\Core\Enums\DocumentVisibility;
use App\Core\Enums\PortalNotificationType;
use App\Core\Services\BaseService;
use App\Domains\Portal\Events\PortalDocumentDownloaded;
use App\Models\Client;
use App\Models\ClientDocument;
use App\Models\PortalDownload;
use App\Models\PortalFavorite;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Support\Collection;

final class PortalService extends BaseService
{
    public function __construct(
        private readonly NotificationService $notifications,
        private readonly DashboardService $dashboard,
    ) {}

    /**
     * @return Collection<int, Quotation>
     */
    public function quotations(Client $client): Collection
    {
        return $client->quotations()
            ->with('request')
            ->orderByDesc('updated_at')
            ->get();
    }

    /**
     * @return Collection<int, Project>
     */
    public function projects(Client $client): Collection
    {
        return $client->projects()
            ->with(['milestones', 'progressUpdates'])
            ->orderBy('title')
            ->get();
    }

    /**
     * @return Collection<int, ClientDocument>
     */
    public function documents(Client $client): Collection
    {
        return $client->documents()
            ->where('visibility', DocumentVisibility::Client)
            ->orderByDesc('created_at')
            ->get();
    }

    public function recordDownload(Client $client, User $user, ClientDocument $document): PortalDownload
    {
        abort_unless($document->client_id === $client->id, 403);
        abort_unless($document->visibility === DocumentVisibility::Client, 403);

        $download = PortalDownload::query()->create([
            'client_id' => $client->id,
            'user_id' => $user->id,
            'client_document_id' => $document->id,
            'label' => $document->title,
            'stored_path' => $document->stored_path,
            'downloaded_at' => now(),
        ]);

        $this->notifications->create(
            $client,
            PortalNotificationType::Document,
            'Document downloaded',
            $document->title,
        );

        event(new PortalDocumentDownloaded($download));
        $this->dashboard->forget($client);

        return $download;
    }

    public function toggleFavorite(Client $client, Project $project): bool
    {
        $existing = PortalFavorite::query()
            ->where('client_id', $client->id)
            ->where('favoritable_type', Project::class)
            ->where('favoritable_id', $project->id)
            ->first();

        if ($existing !== null) {
            $existing->delete();
            $client->projects()->updateExistingPivot($project->id, ['is_favorite' => false]);

            return false;
        }

        PortalFavorite::query()->create([
            'client_id' => $client->id,
            'favoritable_type' => Project::class,
            'favoritable_id' => $project->id,
        ]);

        $client->projects()->syncWithoutDetaching([
            $project->id => ['is_favorite' => true],
        ]);

        return true;
    }
}
