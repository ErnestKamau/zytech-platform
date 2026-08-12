<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <div>
            <p class="zy-muted">Welcome back</p>
            <h1 class="zy-portal-page__title">{{ $data->clientName }}</h1>
        </div>
        <div class="zy-portal-stats">
            <div class="zy-portal-stat"><strong>{{ $data->pendingQuotations }}</strong><span>Pending quotes</span></div>
            <div class="zy-portal-stat"><strong>{{ $data->unreadMessages }}</strong><span>Unread messages</span></div>
            <div class="zy-portal-stat"><strong>{{ $data->unreadNotifications }}</strong><span>Notifications</span></div>
            <div class="zy-portal-stat"><strong>{{ $data->openTickets }}</strong><span>Open tickets</span></div>
        </div>
    </header>

    <div class="zy-portal-grid">
        <section class="zy-portal-panel">
            <h2>Quotations</h2>
            @forelse ($data->quotations as $quote)
                <article class="zy-portal-row">
                    <div>
                        <strong>{{ $quote['reference_number'] }}</strong>
                        <p class="zy-muted">{{ $quote['title'] }}</p>
                    </div>
                    <span class="zy-badge">{{ $quote['status'] }}</span>
                </article>
            @empty
                <p class="zy-muted">No quotations yet.</p>
            @endforelse
            <a href="{{ route('portal.quotations') }}" class="zy-btn zy-btn--secondary zy-btn--sm">View all</a>
        </section>

        <section class="zy-portal-panel">
            <h2>Projects</h2>
            @forelse ($data->projects as $project)
                <article class="zy-portal-row">
                    <div>
                        <strong>{{ $project['title'] }}</strong>
                        <p class="zy-muted">{{ $project['status'] }}</p>
                    </div>
                </article>
            @empty
                <p class="zy-muted">No linked projects yet.</p>
            @endforelse
            <a href="{{ route('portal.projects') }}" class="zy-btn zy-btn--secondary zy-btn--sm">View projects</a>
        </section>

        <section class="zy-portal-panel">
            <h2>Documents</h2>
            @forelse ($data->documents as $document)
                <article class="zy-portal-row">
                    <div>
                        <strong>{{ $document['title'] }}</strong>
                        <p class="zy-muted">{{ $document['kind'] }} · {{ $document['created_at'] }}</p>
                    </div>
                </article>
            @empty
                <p class="zy-muted">No shared documents yet.</p>
            @endforelse
            <a href="{{ route('portal.documents') }}" class="zy-btn zy-btn--secondary zy-btn--sm">Document centre</a>
        </section>

        <section class="zy-portal-panel">
            <h2>Announcements</h2>
            @forelse ($data->announcements as $item)
                <article class="zy-portal-row">
                    <div>
                        <strong>{{ $item['title'] }}</strong>
                        <p class="zy-muted">{{ \Illuminate\Support\Str::limit($item['body'], 120) }}</p>
                    </div>
                </article>
            @empty
                <p class="zy-muted">No announcements.</p>
            @endforelse
        </section>

        <section class="zy-portal-panel">
            <h2>Upcoming meetings</h2>
            @forelse ($data->meetings as $meeting)
                <article class="zy-portal-row">
                    <div>
                        <strong>{{ $meeting['type'] }}</strong>
                        <p class="zy-muted">{{ $meeting['scheduled_at'] ?? $meeting['status'] }}</p>
                    </div>
                </article>
            @empty
                <p class="zy-muted">No upcoming meetings.</p>
            @endforelse
            <a href="{{ route('portal.meetings') }}" class="zy-btn zy-btn--secondary zy-btn--sm">Schedule</a>
        </section>

        <section class="zy-portal-panel">
            <h2>Recent notifications</h2>
            @forelse ($data->notifications as $notification)
                <article class="zy-portal-row">
                    <div>
                        <strong>{{ $notification['title'] }}</strong>
                        <p class="zy-muted">{{ $notification['created_at'] }}</p>
                    </div>
                    @unless ($notification['read'])
                        <span class="zy-badge">New</span>
                    @endunless
                </article>
            @empty
                <p class="zy-muted">You're all caught up.</p>
            @endforelse
            <a href="{{ route('portal.notifications') }}" class="zy-btn zy-btn--secondary zy-btn--sm">Notification centre</a>
        </section>
    </div>
</div>
