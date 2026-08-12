<?php

namespace App\Domains\Portal\Services;

use App\Core\Enums\PortalNotificationType;
use App\Core\Services\BaseService;
use App\Domains\Portal\Events\NotificationCreated;
use App\Models\Client;
use App\Models\PortalNotification;
use Illuminate\Support\Collection;

final class NotificationService extends BaseService
{
    public function __construct(private readonly DashboardService $dashboard) {}

    /**
     * @param  array<string, mixed>|null  $meta
     */
    public function create(
        Client $client,
        PortalNotificationType $type,
        string $title,
        ?string $body = null,
        ?array $meta = null,
    ): PortalNotification {
        $notification = PortalNotification::query()->create([
            'client_id' => $client->id,
            'user_id' => $client->user_id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'meta' => $meta,
        ]);

        event(new NotificationCreated($notification));

        $this->dashboard->forget($client);

        return $notification;
    }

    /**
     * @return Collection<int, PortalNotification>
     */
    public function forClient(Client $client): Collection
    {
        return PortalNotification::query()
            ->where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
    }

    public function markRead(PortalNotification $notification): PortalNotification
    {
        if ($notification->read_at === null) {
            $notification->forceFill(['read_at' => now()])->save();
            $this->dashboard->forget($notification->client);
        }

        return $notification->refresh();
    }

    public function markAllRead(Client $client): void
    {
        PortalNotification::query()
            ->where('client_id', $client->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->dashboard->forget($client);
    }
}
