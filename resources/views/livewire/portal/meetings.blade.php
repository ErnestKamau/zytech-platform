<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <h1 class="zy-portal-page__title">Meetings</h1>
    </header>

    <div class="zy-portal-grid">
        <section class="zy-portal-panel">
            <h2>Request a meeting</h2>
            <form wire:submit="schedule" class="zy-form zy-portal-form">
                <label>Type
                    <select wire:model="meeting_type">
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Available slot
                    <select wire:model="slot_id">
                        <option value="">Flexible / to be confirmed</option>
                        @foreach ($slots as $slot)
                            <option value="{{ $slot->id }}">
                                {{ $slot->starts_at?->toDayDateTimeString() }} ({{ $slot->meeting_type->label() }})
                            </option>
                        @endforeach
                    </select>
                </label>
                <label>Notes<textarea wire:model="notes" rows="3"></textarea></label>
                <button type="submit" class="zy-btn zy-btn--primary zy-btn--sm">Submit request</button>
            </form>
        </section>

        <section class="zy-portal-panel">
            <h2>Your meetings</h2>
            @forelse ($meetings as $meeting)
                <article class="zy-portal-row">
                    <div>
                        <strong>{{ $meeting->meeting_type->label() }}</strong>
                        <p class="zy-muted">{{ $meeting->status->label() }} @if ($meeting->scheduled_at)· {{ $meeting->scheduled_at->toDayDateTimeString() }}@endif</p>
                        @if ($meeting->notes)
                            <p class="zy-muted">{{ $meeting->notes }}</p>
                        @endif
                    </div>
                    @if (in_array($meeting->status, [\App\Core\Enums\MeetingStatus::Requested, \App\Core\Enums\MeetingStatus::Confirmed], true))
                        <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="cancel('{{ $meeting->id }}')" wire:confirm="Cancel this meeting?">Cancel</button>
                    @endif
                </article>
            @empty
                <p class="zy-muted">No meeting requests yet.</p>
            @endforelse
        </section>
    </div>
</div>
