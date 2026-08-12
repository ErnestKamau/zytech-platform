<?php

namespace App\Domains\Client\Services;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientTimelineEvent;
use App\Core\Enums\ClientType;
use App\Core\Services\BaseService;
use App\Domains\Client\Data\ClientData;
use App\Domains\Client\Events\ClientArchived;
use App\Domains\Client\Events\ClientCreated;
use App\Domains\Client\Events\ClientUpdated;
use App\Domains\Client\Events\PortalAccessGranted;
use App\Domains\Client\Repositories\ClientRepository;
use App\Models\Client;
use App\Models\ClientPreference;
use App\Models\ClientStatusHistory;
use Illuminate\Support\Collection;

final class ClientService extends BaseService
{
    public function __construct(
        private readonly ClientRepository $clients,
        private readonly TimelineService $timeline,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Client
    {
        $client = Client::query()->create($attributes);

        ClientPreference::query()->create(['client_id' => $client->id]);

        $this->recordStatus($client, null, $client->status, 'Client profile created');

        $this->timeline->record(
            $client,
            ClientTimelineEvent::LeadCreated,
            'Client profile created',
            'New client record in CRM.',
        );

        event(new ClientCreated($client->fresh(['preferences'])));

        return $client->refresh();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Client $client, array $attributes): Client
    {
        $client->fill($attributes)->save();

        event(new ClientUpdated($client->fresh()));

        return $client->refresh();
    }

    public function archive(Client $client, ?string $notes = null): Client
    {
        $from = $client->status;
        $client->forceFill(['status' => ClientStatus::Archived])->save();

        $this->recordStatus($client, $from, ClientStatus::Archived, $notes);

        $this->timeline->record(
            $client,
            ClientTimelineEvent::StatusChanged,
            'Client archived',
            $notes,
        );

        event(new ClientArchived($client->refresh()));

        return $client;
    }

    public function find(string $id): ?Client
    {
        return $this->clients->findWithRelations($id);
    }

    /**
     * @return Collection<int, ClientData>
     */
    public function activeCards(): Collection
    {
        return $this->clients->active()->map(fn (Client $client): ClientData => $this->toData($client));
    }

    public function findOrCreateFromLead(string $name, string $email, ?string $phone = null): Client
    {
        $existing = $this->clients->findByEmail($email);

        if ($existing !== null) {
            return $existing;
        }

        $type = str_contains($name, ' Ltd') || str_contains($name, ' Limited')
            ? ClientType::Company
            : ClientType::Individual;

        return $this->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'type' => $type,
            'status' => ClientStatus::Prospect,
        ]);
    }

    public function initializeRecord(Client $client): Client
    {
        if ($client->preferences()->exists()) {
            return $client;
        }

        ClientPreference::query()->create(['client_id' => $client->id]);

        $this->recordStatus($client, null, $client->status, 'Client profile created');

        $this->timeline->record(
            $client,
            ClientTimelineEvent::LeadCreated,
            'Client profile created',
            'New client record in CRM.',
        );

        event(new ClientCreated($client->fresh(['preferences'])));

        return $client->refresh();
    }

    public function assignPortalAccess(Client $client, string $userId): Client
    {
        $client->forceFill([
            'user_id' => $userId,
            'portal_access_granted_at' => now(),
        ])->save();

        $this->timeline->record(
            $client,
            ClientTimelineEvent::PortalAccessGranted,
            'Portal access linked',
            'User account connected to client profile.',
        );

        event(new PortalAccessGranted($client->refresh()));

        return $client;
    }

    private function recordStatus(Client $client, ?ClientStatus $from, ClientStatus $to, ?string $notes): void
    {
        ClientStatusHistory::query()->create([
            'client_id' => $client->id,
            'from_status' => $from?->value,
            'to_status' => $to->value,
            'notes' => $notes,
            'changed_by' => auth()->id(),
        ]);
    }

    private function toData(Client $client): ClientData
    {
        return ClientData::fromArray([
            ...$client->toArray(),
            'assigned_sales_name' => $client->assignedSales?->name,
            'quotation_count' => $client->quotations()->count(),
            'project_count' => $client->projects()->count(),
        ]);
    }
}
