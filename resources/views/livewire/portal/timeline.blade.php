<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <div class="zy-portal-page__intro">
            <p class="zy-section__eyebrow">History</p>
            <h1 class="zy-portal-page__title">Activity timeline</h1>
            <p class="zy-portal-page__lead">A chronological view of key moments across your relationship with Zytech.</p>
        </div>
    </header>

    @if ($events->isEmpty())
        <div class="zy-portal-empty">
            <p class="zy-section__eyebrow">Timeline</p>
            <p>No timeline events yet.</p>
        </div>
    @else
        <div class="zy-portal-panel">
            <div class="zy-portal-timeline">
                @foreach ($events as $event)
                    <article class="zy-portal-timeline__item">
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
