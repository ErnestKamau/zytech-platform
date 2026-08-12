<div class="zy-portal-page">
    <section class="zy-portal-hero zy-portal-hero--dash">
        <div class="zy-portal-hero__glow" aria-hidden="true"></div>
        <div class="zy-portal-hero__layout">
            <div class="zy-portal-hero__content">
                <p class="zy-portal-hero__eyebrow">
                    <x-portal.icon name="sparkles" />
                    Client workspace
                </p>
                <h1 class="zy-portal-hero__title">Hello, {{ explode(' ', $data->clientName)[0] ?: $data->clientName }}</h1>
                <p class="zy-portal-hero__lead">Quotations, projects, documents, and conversations — one calm place to move work forward with Zytech.</p>
                <div class="zy-portal-hero__actions">
                    <a href="{{ route('portal.quotations') }}" class="zy-btn zy-btn--frost zy-btn--sm">
                        <x-portal.icon name="document" />
                        View quotations
                    </a>
                    <a href="{{ route('portal.meetings') }}" class="zy-btn zy-btn--primary zy-btn--sm">
                        <x-portal.icon name="calendar" />
                        Request a meeting
                    </a>
                </div>
            </div>
            <div class="zy-portal-hero__aside" aria-hidden="true">
                <div class="zy-portal-hero__orb zy-portal-hero__orb--a"></div>
                <div class="zy-portal-hero__orb zy-portal-hero__orb--b"></div>
            </div>
        </div>
    </section>

    <div class="zy-portal-stats zy-portal-stats--dash" aria-label="At a glance">
        <div class="zy-portal-stat">
            <span class="zy-portal-stat__icon" aria-hidden="true"><x-portal.icon name="document" /></span>
            <strong>{{ $data->pendingQuotations }}</strong>
            <span>Pending quotes</span>
        </div>
        <div class="zy-portal-stat">
            <span class="zy-portal-stat__icon" aria-hidden="true"><x-portal.icon name="chat" /></span>
            <strong>{{ $data->unreadMessages }}</strong>
            <span>Unread messages</span>
        </div>
        <div class="zy-portal-stat">
            <span class="zy-portal-stat__icon" aria-hidden="true"><x-portal.icon name="bell" /></span>
            <strong>{{ $data->unreadNotifications }}</strong>
            <span>Notifications</span>
        </div>
        <div class="zy-portal-stat">
            <span class="zy-portal-stat__icon" aria-hidden="true"><x-portal.icon name="ticket" /></span>
            <strong>{{ $data->openTickets }}</strong>
            <span>Open tickets</span>
        </div>
    </div>

    <div class="zy-portal-dash">
        <div class="zy-portal-dash__main">
            <section class="zy-portal-panel zy-portal-panel--lift">
                <div class="zy-portal-panel__head">
                    <div class="zy-portal-panel__title-wrap">
                        <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="document" /></span>
                        <h2 class="zy-portal-panel__title">Quotations</h2>
                    </div>
                    <a href="{{ route('portal.quotations') }}" class="zy-btn zy-btn--ghost zy-btn--sm">View all</a>
                </div>
                @forelse ($data->quotations as $quote)
                    <article class="zy-portal-row zy-portal-row--pill">
                        <div>
                            <p class="zy-portal-row__title">{{ $quote['reference_number'] }}</p>
                            <p class="zy-muted">{{ $quote['title'] }}</p>
                        </div>
                        <span class="zy-badge zy-badge--primary">{{ $quote['status'] }}</span>
                    </article>
                @empty
                    <x-portal.empty-state icon="document" description="No quotations yet." />
                @endforelse
            </section>

            <section class="zy-portal-panel zy-portal-panel--lift">
                <div class="zy-portal-panel__head">
                    <div class="zy-portal-panel__title-wrap">
                        <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="folder" /></span>
                        <h2 class="zy-portal-panel__title">Projects</h2>
                    </div>
                    <a href="{{ route('portal.projects') }}" class="zy-btn zy-btn--ghost zy-btn--sm">View projects</a>
                </div>
                @forelse ($data->projects as $project)
                    <article class="zy-portal-row zy-portal-row--pill">
                        <div>
                            <p class="zy-portal-row__title">{{ $project['title'] }}</p>
                            <p class="zy-muted">{{ $project['status'] }}</p>
                        </div>
                    </article>
                @empty
                    <x-portal.empty-state icon="folder" description="No linked projects yet." />
                @endforelse
            </section>

            <section class="zy-portal-panel zy-portal-panel--lift">
                <div class="zy-portal-panel__head">
                    <div class="zy-portal-panel__title-wrap">
                        <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="inbox" /></span>
                        <h2 class="zy-portal-panel__title">Documents</h2>
                    </div>
                    <a href="{{ route('portal.documents') }}" class="zy-btn zy-btn--ghost zy-btn--sm">Document centre</a>
                </div>
                @forelse ($data->documents as $document)
                    <article class="zy-portal-row zy-portal-row--pill">
                        <div>
                            <p class="zy-portal-row__title">{{ $document['title'] }}</p>
                            <p class="zy-muted">{{ $document['kind'] }} · {{ $document['created_at'] }}</p>
                        </div>
                    </article>
                @empty
                    <x-portal.empty-state icon="inbox" description="No shared documents yet." />
                @endforelse
            </section>
        </div>

        <aside class="zy-portal-dash__rail">
            <section class="zy-portal-panel zy-portal-panel--activity">
                <div class="zy-portal-panel__head">
                    <div class="zy-portal-panel__title-wrap">
                        <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="bell" /></span>
                        <h2 class="zy-portal-panel__title">Activity</h2>
                    </div>
                    <a href="{{ route('portal.notifications') }}" class="zy-btn zy-btn--ghost zy-btn--sm">Centre</a>
                </div>
                <div class="zy-portal-activity">
                    @forelse ($data->notifications as $notification)
                        <article class="zy-portal-activity__item">
                            <span class="zy-portal-activity__mark" aria-hidden="true"><x-portal.icon name="bell" /></span>
                            <div>
                                <p class="zy-portal-row__title">{{ $notification['title'] }}</p>
                                <p class="zy-muted">{{ $notification['created_at'] }}</p>
                            </div>
                            @unless ($notification['read'])
                                <span class="zy-badge zy-badge--gradient">New</span>
                            @endunless
                        </article>
                    @empty
                        <x-portal.empty-state icon="bell" description="You're all caught up." />
                    @endforelse
                </div>
            </section>

            <section class="zy-portal-panel zy-portal-panel--lift">
                <div class="zy-portal-panel__head">
                    <div class="zy-portal-panel__title-wrap">
                        <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="calendar" /></span>
                        <h2 class="zy-portal-panel__title">Upcoming</h2>
                    </div>
                    <a href="{{ route('portal.meetings') }}" class="zy-btn zy-btn--ghost zy-btn--sm">Schedule</a>
                </div>
                @forelse ($data->meetings as $meeting)
                    <article class="zy-portal-activity__item">
                        <span class="zy-portal-activity__mark" aria-hidden="true"><x-portal.icon name="calendar" /></span>
                        <div>
                            <p class="zy-portal-row__title">{{ $meeting['type'] }}</p>
                            <p class="zy-muted">{{ $meeting['scheduled_at'] ?? $meeting['status'] }}</p>
                        </div>
                    </article>
                @empty
                    <x-portal.empty-state icon="calendar" description="No upcoming meetings." />
                @endforelse
            </section>

            <section class="zy-portal-panel zy-portal-panel--lift">
                <div class="zy-portal-panel__head">
                    <div class="zy-portal-panel__title-wrap">
                        <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="megaphone" /></span>
                        <h2 class="zy-portal-panel__title">Announcements</h2>
                    </div>
                </div>
                @forelse ($data->announcements as $item)
                    <article class="zy-portal-row">
                        <div>
                            <p class="zy-portal-row__title">{{ $item['title'] }}</p>
                            <p class="zy-muted">{{ \Illuminate\Support\Str::limit($item['body'], 120) }}</p>
                        </div>
                    </article>
                @empty
                    <x-portal.empty-state icon="megaphone" description="No announcements." />
                @endforelse
            </section>
        </aside>
    </div>
</div>
