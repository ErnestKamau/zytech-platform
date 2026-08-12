<?php

namespace App\Domains\Portal\Repositories;

use App\Models\Client;
use App\Models\PortalConversation;
use Illuminate\Support\Collection;

final class MessageRepository
{
    /**
     * @return Collection<int, PortalConversation>
     */
    public function forClient(Client $client): Collection
    {
        return PortalConversation::query()
            ->with(['messages.author', 'assignee'])
            ->where('client_id', $client->id)
            ->orderByDesc('last_message_at')
            ->get();
    }

    public function findForClient(Client $client, string $id): ?PortalConversation
    {
        return PortalConversation::query()
            ->with(['messages.author'])
            ->where('client_id', $client->id)
            ->find($id);
    }
}
