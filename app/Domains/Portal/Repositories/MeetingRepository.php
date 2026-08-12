<?php

namespace App\Domains\Portal\Repositories;

use App\Core\Enums\MeetingStatus;
use App\Models\Client;
use App\Models\MeetingRequest;
use App\Models\MeetingSlot;
use Illuminate\Support\Collection;

final class MeetingRepository
{
    /**
     * @return Collection<int, MeetingRequest>
     */
    public function forClient(Client $client): Collection
    {
        return MeetingRequest::query()
            ->with('slot')
            ->where('client_id', $client->id)
            ->orderByDesc('scheduled_at')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @return Collection<int, MeetingSlot>
     */
    public function availableSlots(): Collection
    {
        return MeetingSlot::query()
            ->where('is_available', true)
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(20)
            ->get();
    }

    /**
     * @return Collection<int, MeetingRequest>
     */
    public function upcoming(Client $client): Collection
    {
        return MeetingRequest::query()
            ->where('client_id', $client->id)
            ->whereIn('status', [MeetingStatus::Requested, MeetingStatus::Confirmed])
            ->where(function ($query): void {
                $query->whereNull('scheduled_at')->orWhere('scheduled_at', '>=', now());
            })
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();
    }
}
