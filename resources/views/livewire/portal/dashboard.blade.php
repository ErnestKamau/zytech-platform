@php
    $firstName = explode(' ', $data->clientName)[0] ?: $data->clientName;
    $finance = $data->finance;
    $documents = $data->documents;
    $messages = $data->messages;
@endphp

<div class="zy-portal-page">
    <section class="zy-portal-hero zy-portal-hero--dash">
        <div class="zy-portal-hero__glow" aria-hidden="true"></div>
        <div class="zy-portal-hero__layout">
            <div class="zy-portal-hero__content">
                <p class="zy-portal-hero__eyebrow">
                    <x-portal.icon name="sparkles" />
                    Client workspace
                </p>
                <h1 class="zy-portal-hero__title">Hello, {{ $firstName }}</h1>
                <p class="zy-portal-hero__lead">Your projects, quotations, documents and conversations — everything you need to work with Zytech in one place.</p>
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
            <span>Pending quotations</span>
            <p class="zy-portal-stat__hint">
                {{ $data->pendingQuotations === 1 ? '1 awaiting review' : ($data->pendingQuotations.' awaiting review') }}
            </p>
        </div>
        <div class="zy-portal-stat">
            <span class="zy-portal-stat__icon" aria-hidden="true"><x-portal.icon name="folder" /></span>
            <strong>{{ $data->activeProjects }}</strong>
            <span>Active projects</span>
            <p class="zy-portal-stat__hint">
                {{ $data->activeProjects === 0 ? 'None in progress' : 'Currently linked to you' }}
            </p>
        </div>
        <div class="zy-portal-stat">
            <span class="zy-portal-stat__icon" aria-hidden="true"><x-portal.icon name="document" /></span>
            <strong>{{ \App\Support\Helpers\MoneyFormatter::compact($data->outstanding) }}</strong>
            <span>Outstanding</span>
            <p class="zy-portal-stat__hint">
                {{ $data->openInvoices }} open {{ \Illuminate\Support\Str::plural('invoice', $data->openInvoices) }}
            </p>
        </div>
        <div class="zy-portal-stat">
            <span class="zy-portal-stat__icon" aria-hidden="true"><x-portal.icon name="bell" /></span>
            <strong>{{ $data->unreadTotal }}</strong>
            <span>Unread</span>
            <p class="zy-portal-stat__hint">
                {{ $data->unreadMessages }} {{ \Illuminate\Support\Str::plural('message', $data->unreadMessages) }}
                · {{ $data->unreadNotifications }} {{ \Illuminate\Support\Str::plural('notification', $data->unreadNotifications) }}
            </p>
        </div>
    </div>

    <section class="zy-portal-panel zy-portal-panel--actions zy-portal-panel--lift" aria-labelledby="portal-action-required">
        <div class="zy-portal-panel__head">
            <div class="zy-portal-panel__title-wrap">
                <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="sparkles" /></span>
                <h2 class="zy-portal-panel__title" id="portal-action-required">Action required</h2>
            </div>
        </div>

        @if ($data->actionItems === [])
            <x-portal.empty-state icon="sparkles" description="You’re all caught up. New items that need your attention will appear here." />
        @else
            <div class="zy-portal-actions-list">
                @foreach ($data->actionItems as $item)
                    <article class="zy-portal-action zy-portal-action--{{ $item['severity'] }}">
                        <div class="zy-portal-action__body">
                            <p class="zy-portal-action__title">{{ $item['title'] }}</p>
                            <p class="zy-muted">{{ $item['meta'] }}</p>
                        </div>
                        <a href="{{ $item['url'] }}" class="zy-btn zy-btn--primary zy-btn--sm">{{ $item['action_label'] }}</a>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <div class="zy-portal-dash-grid">
        <section class="zy-portal-panel zy-portal-panel--lift">
            <div class="zy-portal-panel__head">
                <div class="zy-portal-panel__title-wrap">
                    <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="document" /></span>
                    <h2 class="zy-portal-panel__title">My quotations</h2>
                </div>
                <a href="{{ route('portal.quotations') }}" class="zy-btn zy-btn--ghost zy-btn--sm">View all</a>
            </div>
            @forelse ($data->quotations as $quote)
                <article class="zy-portal-row zy-portal-row--pill">
                    <div>
                        <p class="zy-portal-row__title">{{ $quote['reference_number'] }}</p>
                        <p class="zy-muted">{{ $quote['title'] }} · {{ $quote['amount_label'] }}</p>
                    </div>
                    <div class="zy-portal-row__end">
                        <span class="zy-badge zy-badge--primary">{{ $quote['status'] }}</span>
                        @if ($quote['reviewable'])
                            <a href="{{ route('portal.quotations') }}" class="zy-btn zy-btn--ghost zy-btn--sm">Review</a>
                        @endif
                    </div>
                </article>
            @empty
                <x-portal.empty-state icon="document" description="No quotations yet. When Zytech prepares a quotation for you, it will appear here." />
            @endforelse
        </section>

        <section class="zy-portal-panel zy-portal-panel--lift">
            <div class="zy-portal-panel__head">
                <div class="zy-portal-panel__title-wrap">
                    <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="folder" /></span>
                    <h2 class="zy-portal-panel__title">My projects</h2>
                </div>
                <a href="{{ route('portal.projects') }}" class="zy-btn zy-btn--ghost zy-btn--sm">View projects</a>
            </div>
            @forelse ($data->projects as $project)
                <article class="zy-portal-project">
                    <div class="zy-portal-project__head">
                        <p class="zy-portal-row__title">{{ $project['title'] }}</p>
                        <span class="zy-badge">{{ $project['status'] }}</span>
                    </div>
                    <div class="zy-portal-progress" role="progressbar" aria-valuenow="{{ $project['progress_percent'] }}" aria-valuemin="0" aria-valuemax="100">
                        <span class="zy-portal-progress__bar" style="width: {{ min(100, max(0, $project['progress_percent'])) }}%"></span>
                    </div>
                    <p class="zy-muted">
                        {{ $project['progress_percent'] }}%
                        @if ($project['phase'])
                            · {{ $project['phase'] }}
                        @endif
                    </p>
                </article>
            @empty
                <x-portal.empty-state icon="folder" description="No linked projects yet. Your Zytech projects will appear here once they are connected to your account." />
            @endforelse
        </section>

        <section class="zy-portal-panel zy-portal-panel--lift">
            <div class="zy-portal-panel__head">
                <div class="zy-portal-panel__title-wrap">
                    <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="building" /></span>
                    <h2 class="zy-portal-panel__title">My orders</h2>
                </div>
                <a href="{{ route('portal.orders') }}" class="zy-btn zy-btn--ghost zy-btn--sm">View orders</a>
            </div>
            @forelse ($data->orders as $order)
                <article class="zy-portal-row zy-portal-row--pill">
                    <div>
                        <p class="zy-portal-row__title">{{ $order['order_number'] }}</p>
                        <p class="zy-muted">{{ $order['amount_label'] }}</p>
                    </div>
                    <span class="zy-badge zy-badge--primary">{{ $order['status'] }}</span>
                </article>
            @empty
                <x-portal.empty-state icon="building" description="No active orders yet. Construction product orders will appear here." />
            @endforelse
        </section>

        <section class="zy-portal-panel zy-portal-panel--lift">
            <div class="zy-portal-panel__head">
                <div class="zy-portal-panel__title-wrap">
                    <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="ticket" /></span>
                    <h2 class="zy-portal-panel__title">Financial summary</h2>
                </div>
                <a href="{{ route('portal.invoices') }}" class="zy-btn zy-btn--ghost zy-btn--sm">Invoices</a>
            </div>
            <div class="zy-portal-finance">
                <div>
                    <p class="zy-muted">Outstanding</p>
                    <p class="zy-portal-finance__value">{{ $finance['outstanding_label'] ?? 'KES 0.00' }}</p>
                </div>
                <div>
                    <p class="zy-muted">Open invoices</p>
                    <p class="zy-portal-finance__value">{{ $finance['open_invoices'] ?? 0 }}</p>
                </div>
                <div>
                    <p class="zy-muted">Overdue</p>
                    <p class="zy-portal-finance__value">{{ $finance['overdue'] ?? 0 }}</p>
                </div>
                <div>
                    <p class="zy-muted">Paid this year</p>
                    <p class="zy-portal-finance__value">{{ $finance['paid_this_year_label'] ?? 'KES 0.00' }}</p>
                </div>
            </div>
        </section>

        <section class="zy-portal-panel zy-portal-panel--lift">
            <div class="zy-portal-panel__head">
                <div class="zy-portal-panel__title-wrap">
                    <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="inbox" /></span>
                    <h2 class="zy-portal-panel__title">Documents</h2>
                </div>
                <a href="{{ route('portal.documents') }}" class="zy-btn zy-btn--ghost zy-btn--sm">Open centre</a>
            </div>
            <p class="zy-portal-panel__summary">
                {{ $documents['total'] ?? 0 }} shared
                @if (($documents['new'] ?? 0) > 0)
                    · {{ $documents['new'] }} new
                @endif
            </p>
            @forelse (($documents['latest'] ?? []) as $document)
                <article class="zy-portal-row zy-portal-row--pill">
                    <div>
                        <p class="zy-portal-row__title">{{ $document['title'] }}</p>
                        <p class="zy-muted">{{ $document['kind'] }} · {{ $document['created_at'] }}</p>
                    </div>
                </article>
            @empty
                <x-portal.empty-state icon="inbox" description="No shared documents yet. Documents shared by the Zytech team will appear here." />
            @endforelse
        </section>

        <section class="zy-portal-panel zy-portal-panel--lift">
            <div class="zy-portal-panel__head">
                <div class="zy-portal-panel__title-wrap">
                    <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="chat" /></span>
                    <h2 class="zy-portal-panel__title">Messages</h2>
                </div>
                <a href="{{ route('portal.messages') }}" class="zy-btn zy-btn--ghost zy-btn--sm">Open messages</a>
            </div>
            <p class="zy-portal-panel__summary">{{ $messages['unread'] ?? 0 }} unread</p>
            @forelse (($messages['preview'] ?? []) as $conversation)
                <article class="zy-portal-row zy-portal-row--pill">
                    <div>
                        <p class="zy-portal-row__title">{{ $conversation['subject'] }}</p>
                        <p class="zy-muted">
                            @if ($conversation['author'])
                                {{ $conversation['author'] }} ·
                            @endif
                            {{ $conversation['snippet'] ?? 'No messages yet' }}
                        </p>
                    </div>
                    <span class="zy-muted">{{ $conversation['when'] }}</span>
                </article>
            @empty
                <x-portal.empty-state icon="chat" description="No conversations yet. Message the Zytech team when you need clarification." />
            @endforelse
        </section>
    </div>

    <section class="zy-portal-panel zy-portal-panel--activity zy-portal-panel--lift">
        <div class="zy-portal-panel__head">
            <div class="zy-portal-panel__title-wrap">
                <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="bell" /></span>
                <h2 class="zy-portal-panel__title">Recent activity</h2>
            </div>
            <a href="{{ route('portal.timeline') }}" class="zy-btn zy-btn--ghost zy-btn--sm">Timeline</a>
        </div>
        <div class="zy-portal-activity">
            @forelse ($data->activity as $item)
                <article class="zy-portal-activity__item">
                    <span class="zy-portal-activity__mark" aria-hidden="true"><x-portal.icon name="bell" /></span>
                    <div>
                        <p class="zy-portal-row__title">{{ $item['title'] }}</p>
                        @if ($item['description'])
                            <p class="zy-muted">{{ \Illuminate\Support\Str::limit($item['description'], 100) }}</p>
                        @endif
                    </div>
                    <span class="zy-muted">{{ $item['when'] }}</span>
                </article>
            @empty
                <x-portal.empty-state icon="bell" description="You’re all caught up. New activity will appear here." />
            @endforelse
        </div>
    </section>

    <div class="zy-portal-dash-grid zy-portal-dash-grid--footer">
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
                <x-portal.empty-state icon="calendar" description="No upcoming meetings. Your scheduled meetings will appear here." />
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
                        <p class="zy-muted">{{ \Illuminate\Support\Str::limit($item['body'], 140) }}</p>
                    </div>
                </article>
            @empty
                <x-portal.empty-state icon="megaphone" description="No announcements right now." />
            @endforelse
        </section>
    </div>
</div>
