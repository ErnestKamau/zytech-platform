<?php

namespace App\Domains\Portal\Repositories;

use App\Models\Client;
use App\Models\SupportTicket;
use Illuminate\Support\Collection;

final class SupportRepository
{
    /**
     * @return Collection<int, SupportTicket>
     */
    public function forClient(Client $client): Collection
    {
        return SupportTicket::query()
            ->with(['replies.author'])
            ->where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function findForClient(Client $client, string $id): ?SupportTicket
    {
        return SupportTicket::query()
            ->with(['replies.author'])
            ->where('client_id', $client->id)
            ->find($id);
    }
}
