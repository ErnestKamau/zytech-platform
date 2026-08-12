<?php

namespace App\Domains\Portal\Services;

use App\Core\Enums\MeetingStatus;
use App\Core\Enums\MeetingType;
use App\Core\Enums\PortalNotificationType;
use App\Core\Services\BaseService;
use App\Domains\Portal\Events\MeetingCancelled;
use App\Domains\Portal\Events\MeetingScheduled;
use App\Domains\Portal\Repositories\MeetingRepository;
use App\Models\Client;
use App\Models\MeetingRequest;
use App\Models\MeetingSlot;
use Illuminate\Support\Collection;

final class MeetingService extends BaseService
{
    public function __construct(
        private readonly MeetingRepository $meetings,
        private readonly NotificationService $notifications,
        private readonly DashboardService $dashboard,
    ) {}

    /**
     * @return Collection<int, MeetingRequest>
     */
    public function forClient(Client $client): Collection
    {
        return $this->meetings->forClient($client);
    }

    /**
     * @return Collection<int, MeetingSlot>
     */
    public function availableSlots(): Collection
    {
        return $this->meetings->availableSlots();
    }

    public function schedule(
        Client $client,
        MeetingType $type,
        ?string $notes = null,
        ?string $slotId = null,
        ?string $scheduledAt = null,
        ?string $location = null,
    ): MeetingRequest {
        $slot = null;
        $startsAt = $scheduledAt ? now()->parse($scheduledAt) : null;

        if ($slotId !== null) {
            $slot = MeetingSlot::query()->where('is_available', true)->findOrFail($slotId);
            $startsAt = $slot->starts_at;
            $slot->forceFill(['is_available' => false])->save();
        }

        $request = MeetingRequest::query()->create([
            'client_id' => $client->id,
            'meeting_slot_id' => $slot?->id,
            'meeting_type' => $type,
            'status' => MeetingStatus::Requested,
            'scheduled_at' => $startsAt,
            'location' => $location,
            'notes' => $notes,
        ]);

        $this->notifications->create(
            $client,
            PortalNotificationType::Meeting,
            'Meeting requested',
            $type->label().($startsAt ? ' — '.$startsAt->toDayDateTimeString() : ''),
        );

        event(new MeetingScheduled($request->fresh(['slot'])));
        $this->dashboard->forget($client);

        return $request;
    }

    public function cancel(MeetingRequest $request): MeetingRequest
    {
        $request->forceFill(['status' => MeetingStatus::Cancelled])->save();

        if ($request->slot) {
            $request->slot->forceFill(['is_available' => true])->save();
        }

        event(new MeetingCancelled($request->refresh()));

        if ($request->client) {
            $this->dashboard->forget($request->client);
        }

        return $request;
    }
}
