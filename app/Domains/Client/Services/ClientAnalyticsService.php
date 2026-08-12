<?php

namespace App\Domains\Client\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Services\BaseService;
use App\Domains\Client\Support\ClientCache;
use App\Models\Client;
use App\Models\ClientCommunication;
use App\Models\ClientDocument;

final class ClientAnalyticsService extends BaseService
{
    public function __construct(private readonly CacheStore $cache) {}

    /**
     * @return array<string, int|float>
     */
    public function dashboard(): array
    {
        $cached = $this->cache->get(ClientCache::DASHBOARD);

        if (is_array($cached)) {
            return $cached;
        }

        $snapshot = [
            'clients_total' => Client::query()->count(),
            'clients_active' => Client::query()->where('status', 'active')->count(),
            'clients_prospect' => Client::query()->where('status', 'prospect')->count(),
            'documents_total' => ClientDocument::query()->count(),
            'communications_total' => ClientCommunication::query()->count(),
            'portal_enabled' => Client::query()->whereNotNull('portal_access_granted_at')->count(),
        ];

        $this->cache->put(ClientCache::DASHBOARD, $snapshot, now()->addMinutes(15));

        return $snapshot;
    }

    public function forget(): void
    {
        ClientCache::forget($this->cache);
    }
}
