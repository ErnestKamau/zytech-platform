<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Updates"
        title="Notifications"
        lead="Project, quotation, document, and support alerts for your account."
        icon="bell"
    >
        <button type="button" class="zy-btn zy-btn--secondary zy-btn--sm" wire:click="markAll">Mark all read</button>
    </x-portal.page-header>

    <x-portal.list-toolbar
        search-model="search"
        filter-model="read"
        :filter-options="$readOptions"
        filter-label="Read status"
        placeholder="Search notifications…"
        export-action="export"
    />

    <div wire:loading.delay class="zy-portal-stack" style="margin-bottom: var(--zy-space-4);">
        <x-ui.skeleton-grid :count="3" variant="line" />
    </div>

    <div class="zy-portal-stack" wire:loading.delay.remove>
        @forelse ($notifications as $notification)
            <article class="zy-portal-panel zy-portal-panel--lift @if ($notification->isUnread()) is-unread @endif">
                <div class="zy-portal-row">
                    <div class="zy-portal-panel__title-wrap" style="align-items: start;">
                        <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="bell" /></span>
                        <div>
                            <p class="zy-eyebrow">{{ $notification->type?->label() ?? 'Update' }}</p>
                            <h2 class="zy-portal-panel__title">{{ $notification->title }}</h2>
                            @if ($notification->body)
                                <p class="zy-muted">{{ $notification->body }}</p>
                            @endif
                            <p class="zy-muted">{{ $notification->created_at?->diffForHumans() }}</p>
                        </div>
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
            <x-ui.empty-state
                class="zy-portal-panel"
                title="No notifications"
                description="No notifications right now."
                :lottie="asset('media/lottie/no-connection.lottie')"
            />
        @endforelse
    </div>
</div>
