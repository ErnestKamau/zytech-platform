<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <div class="zy-portal-page__intro">
            <p class="zy-section__eyebrow">Updates</p>
            <h1 class="zy-portal-page__title">Notifications</h1>
            <p class="zy-portal-page__lead">Project, quotation, document, and support alerts for your account.</p>
        </div>
        <div class="zy-portal-page__actions">
            <button type="button" class="zy-btn zy-btn--secondary zy-btn--sm" wire:click="markAll">Mark all read</button>
        </div>
    </header>

    <div class="zy-portal-stack">
        @forelse ($notifications as $notification)
            <article class="zy-portal-panel @if ($notification->isUnread()) is-unread @endif">
                <div class="zy-portal-row">
                    <div>
                        <p class="zy-eyebrow">{{ $notification->type?->label() ?? 'Update' }}</p>
                        <h2 class="zy-portal-panel__title">{{ $notification->title }}</h2>
                        @if ($notification->body)
                            <p class="zy-muted">{{ $notification->body }}</p>
                        @endif
                        <p class="zy-muted">{{ $notification->created_at?->diffForHumans() }}</p>
                    </div>
                    <div class="zy-portal-actions">
                        @if ($notification->isUnread())
                            <span class="zy-badge zy-badge--gradient">New</span>
                            <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="markRead('{{ $notification->id }}')">Mark read</button>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="zy-portal-empty">
                <p class="zy-section__eyebrow">All clear</p>
                <p>No notifications right now.</p>
            </div>
        @endforelse
    </div>
</div>
