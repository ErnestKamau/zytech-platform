<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <h1 class="zy-portal-page__title">Activity timeline</h1>
    </header>

    <div class="zy-portal-stack">
        @forelse ($events as $event)
            <article class="zy-portal-panel">
                <strong>{{ $event->title }}</strong>
                <p class="zy-muted">{{ $event->event_type->label() }} · {{ $event->occurred_at?->toDayDateTimeString() }}</p>
                @if ($event->description)
                    <p>{{ $event->description }}</p>
                @endif
            </article>
        @empty
            <p class="zy-muted">No timeline events yet.</p>
        @endforelse
    </div>
</div>
