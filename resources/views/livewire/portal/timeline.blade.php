<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="History"
        title="Activity timeline"
        lead="A chronological view of key moments across your relationship with Zytech."
        icon="clock"
    />

    @if ($events->isEmpty())
        <x-portal.empty-state
            class="zy-portal-panel"
            eyebrow="Timeline"
            icon="clock"
            title="No events yet"
            description="No timeline events yet."
        />
    @else
        <div class="zy-portal-panel">
            <div class="zy-portal-timeline">
                @foreach ($events as $event)
                    <article class="zy-portal-timeline__item">
                        <span class="zy-portal-timeline__icon" aria-hidden="true">
                            <x-portal.icon name="check" />
                        </span>
                        <p class="zy-eyebrow">{{ $event->event_type->label() }}</p>
                        <h2 class="zy-portal-panel__title">{{ $event->title }}</h2>
                        <p class="zy-muted">{{ $event->occurred_at?->toDayDateTimeString() }}</p>
                        @if ($event->description)
                            <p class="zy-muted">{{ $event->description }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</div>
