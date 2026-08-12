<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <h1 class="zy-portal-page__title">Notifications</h1>
        <button type="button" class="zy-btn zy-btn--secondary zy-btn--sm" wire:click="markAll">Mark all read</button>
    </header>

    <div class="zy-portal-stack">
        @forelse ($notifications as $notification)
            <article class="zy-portal-panel">
                <div class="zy-portal-row">
                    <div>
                        <strong>{{ $notification->title }}</strong>
                        <p class="zy-muted">{{ $notification->body }}</p>
                        <p class="zy-muted">{{ $notification->created_at?->diffForHumans() }}</p>
                    </div>
                    @if ($notification->isUnread())
                        <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="markRead('{{ $notification->id }}')">Mark read</button>
                    @endif
                </div>
            </article>
        @empty
            <p class="zy-muted">No notifications.</p>
        @endforelse
    </div>
</div>
