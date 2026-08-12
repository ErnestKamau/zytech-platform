<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Enums\MeetingStatus;
use App\Core\Enums\MeetingType;
use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Actions\CancelMeeting;
use App\Domains\Portal\Actions\ScheduleMeeting;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\MeetingService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.portal')]
#[Title('Meetings')]
final class Meetings extends BaseComponent
{
    use ResolvesPortalClient;

    public string $meeting_type = 'consultation';

    public string $notes = '';

    public ?string $slot_id = null;

    public function schedule(ScheduleMeeting $action): void
    {
        $this->validate([
            'meeting_type' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'slot_id' => ['nullable', 'string'],
        ]);

        $action->handle(
            $this->portalClient(),
            MeetingType::from($this->meeting_type),
            $this->notes !== '' ? $this->notes : null,
            $this->slot_id !== '' && $this->slot_id !== null ? $this->slot_id : null,
        );

        $this->reset(['notes', 'slot_id']);
        session()->flash('status', 'Meeting request submitted.');
    }

    public function cancel(string $id, MeetingService $meetings, CancelMeeting $action): void
    {
        $meeting = $meetings->forClient($this->portalClient())->firstWhere('id', $id);
        abort_unless($meeting !== null, 404);
        abort_unless(in_array($meeting->status, [MeetingStatus::Requested, MeetingStatus::Confirmed], true), 403);
        $action->handle($meeting);
        session()->flash('status', 'Meeting cancelled.');
    }

    public function render(MeetingService $meetings): View
    {
        return view('livewire.portal.meetings', [
            'meetings' => $meetings->forClient($this->portalClient()),
            'slots' => $meetings->availableSlots(),
            'types' => MeetingType::cases(),
        ]);
    }
}
