<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <div class="zy-portal-page__intro">
            <p class="zy-section__eyebrow">Welcome back</p>
            <h1 class="zy-portal-page__title">{{ $data->clientName }}</h1>
            <p class="zy-portal-page__lead">Your workspace for quotations, projects, documents, and conversations with Zytech.</p>
        </div>
        <div class="zy-portal-stats" aria-label="At a glance">
            <div class="zy-portal-stat"><strong>{{ $data->pendingQuotations }}</strong><span>Pending quotes</span></div>
            <div class="zy-portal-stat"><strong>{{ $data->unreadMessages }}</strong><span>Unread messages</span></div>
            <div class="zy-portal-stat"><strong>{{ $data->unreadNotifications }}</strong><span>Notifications</span></div>
            <div class="zy-portal-stat"><strong>{{ $data->openTickets }}</strong><span>Open tickets</span></div>
        </div>
    </header>

    <div class="zy-portal-grid">
        <section class="zy-portal-panel">
            <div class="zy-portal-panel__head">
                <h2 class="zy-portal-panel__title">Quotations</h2>
                <a href="{{ route('portal.quotations') }}" class="zy-btn zy-btn--ghost zy-btn--sm">View all</a>
            </div>
            @forelse ($data->quotations as $quote)
                <article class="zy-portal-row">
                    <div>
                        <p class="zy-portal-row__title">{{ $quote['reference_number'] }}</p>
                        <p class="zy-muted">{{ $quote['title'] }}</p>
                    </div>
                    <span class="zy-badge zy-badge--primary">{{ $quote['status'] }}</span>
                </article>
            @empty
                <p class="zy-muted">No quotations yet.</p>
            @endforelse
        </section>

        <section class="zy-portal-panel">
            <div class="zy-portal-panel__head">
                <h2 class="zy-portal-panel__title">Projects</h2>
                <a href="{{ route('portal.projects') }}" class="zy-btn zy-btn--ghost zy-btn--sm">View projects</a>
            </div>
            @forelse ($data->projects as $project)
                <article class="zy-portal-row">
                    <div>
                        <p class="zy-portal-row__title">{{ $project['title'] }}</p>
                        <p class="zy-muted">{{ $project['status'] }}</p>
                    </div>
                </article>
            @empty
                <p class="zy-muted">No linked projects yet.</p>
            @endforelse
        </section>

        <section class="zy-portal-panel">
            <div class="zy-portal-panel__head">
                <h2 class="zy-portal-panel__title">Documents</h2>
                <a href="{{ route('portal.documents') }}" class="zy-btn zy-btn--ghost zy-btn--sm">Document centre</a>
            </div>
            @forelse ($data->documents as $document)
                <article class="zy-portal-row">
                    <div>
                        <p class="zy-portal-row__title">{{ $document['title'] }}</p>
                        <p class="zy-muted">{{ $document['kind'] }} · {{ $document['created_at'] }}</p>
                    </div>
                </article>
            @empty
                <p class="zy-muted">No shared documents yet.</p>
            @endforelse
        </section>

        <section class="zy-portal-panel">
            <div class="zy-portal-panel__head">
                <h2 class="zy-portal-panel__title">Announcements</h2>
            </div>
            @forelse ($data->announcements as $item)
                <article class="zy-portal-row">
                    <div>
                        <p class="zy-portal-row__title">{{ $item['title'] }}</p>
                        <p class="zy-muted">{{ \Illuminate\Support\Str::limit($item['body'], 120) }}</p>
                    </div>
                </article>
            @empty
                <p class="zy-muted">No announcements.</p>
            @endforelse
        </section>

        <section class="zy-portal-panel">
            <div class="zy-portal-panel__head">
                <h2 class="zy-portal-panel__title">Upcoming meetings</h2>
                <a href="{{ route('portal.meetings') }}" class="zy-btn zy-btn--ghost zy-btn--sm">Schedule</a>
            </div>
            @forelse ($data->meetings as $meeting)
                <article class="zy-portal-row">
                    <div>
                        <p class="zy-portal-row__title">{{ $meeting['type'] }}</p>
                        <p class="zy-muted">{{ $meeting['scheduled_at'] ?? $meeting['status'] }}</p>
                    </div>
                </article>
            @empty
                <p class="zy-muted">No upcoming meetings.</p>
            @endforelse
        </section>

        <section class="zy-portal-panel">
            <div class="zy-portal-panel__head">
                <h2 class="zy-portal-panel__title">Recent notifications</h2>
                <a href="{{ route('portal.notifications') }}" class="zy-btn zy-btn--ghost zy-btn--sm">Centre</a>
            </div>
            @forelse ($data->notifications as $notification)
                <article class="zy-portal-row">
                    <div>
                        <p class="zy-portal-row__title">{{ $notification['title'] }}</p>
                        <p class="zy-muted">{{ $notification['created_at'] }}</p>
                    </div>
                    @unless ($notification['read'])
                        <span class="zy-badge zy-badge--gradient">New</span>
                    @endunless
                </article>
            @empty
                <p class="zy-muted">You're all caught up.</p>
            @endforelse
        </section>
    </div>
</div>
