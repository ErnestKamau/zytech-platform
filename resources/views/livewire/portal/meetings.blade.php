<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Schedule"
        title="Meetings"
        lead="Request a site visit, consultation, or review. Available slots appear when the team publishes them."
        icon="calendar"
    />

    <div class="zy-portal-grid">
        <section class="zy-portal-panel">
            <div class="zy-portal-panel__title-wrap">
                <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="plus" /></span>
                <h2 class="zy-portal-panel__title">Request a meeting</h2>
            </div>
            <form wire:submit="schedule" class="zy-stack">
                <div class="zy-field">
                    <label class="zy-label" for="meeting-type">Type</label>
                    <select id="meeting-type" class="zy-select" wire:model="meeting_type">
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="zy-field">
                    <label class="zy-label" for="meeting-slot">Available slot</label>
                    <select id="meeting-slot" class="zy-select" wire:model="slot_id">
                        <option value="">Flexible / to be confirmed</option>
                        @foreach ($slots as $slot)
                            <option value="{{ $slot->id }}">
                                {{ $slot->starts_at?->toDayDateTimeString() }} ({{ $slot->meeting_type->label() }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="zy-field">
                    <label class="zy-label" for="meeting-notes">Notes</label>
                    <textarea id="meeting-notes" class="zy-textarea" wire:model="notes" rows="3"></textarea>
                </div>
                <button type="submit" class="zy-btn zy-btn--primary zy-btn--sm">Submit request</button>
            </form>
        </section>

        <section class="zy-portal-panel">
            <div class="zy-portal-panel__title-wrap">
                <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="calendar" /></span>
                <h2 class="zy-portal-panel__title">Your meetings</h2>
            </div>
            @forelse ($meetings as $meeting)
                <article class="zy-portal-row">
                    <div>
                        <p class="zy-portal-row__title">{{ $meeting->meeting_type->label() }}</p>
                        <p class="zy-portal-row__meta">
                            {{ $meeting->status->label() }}
                            @if ($meeting->scheduled_at)
                                · {{ $meeting->scheduled_at->toDayDateTimeString() }}
                            @endif
                        </p>
                        @if ($meeting->notes)
                            <p class="zy-portal-row__meta">{{ $meeting->notes }}</p>
                        @endif
                    </div>
                    <div class="zy-portal-actions">
                        <span class="zy-badge zy-badge--neutral">{{ $meeting->status->label() }}</span>
                        @if (in_array($meeting->status, [\App\Core\Enums\MeetingStatus::Requested, \App\Core\Enums\MeetingStatus::Confirmed], true))
                            <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="cancel('{{ $meeting->id }}')" wire:confirm="Cancel this meeting?">Cancel</button>
                        @endif
                    </div>
                </article>
            @empty
                <x-portal.empty-state icon="calendar" description="No meeting requests yet." />
            @endforelse
        </section>
    </div>
</div>
