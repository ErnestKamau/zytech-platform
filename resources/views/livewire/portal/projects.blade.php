<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Delivery"
        title="Projects"
        lead="Track linked projects, milestones, and the latest progress updates from the Zytech team."
        icon="folder"
    />

    <x-portal.list-toolbar
        search-model="search"
        filter-model="status"
        :filter-options="$statusOptions"
        filter-label="Status"
        placeholder="Search projects…"
        export-action="export"
    />

    @if ($featured->isNotEmpty())
        <div class="zy-portal-featured">
            @foreach ($featured as $project)
                <article class="zy-portal-featured__card">
                    <span class="zy-badge">{{ $project->statusLabel() }}</span>
                    <h2 class="zy-portal-panel__title" style="margin-top: var(--zy-space-3);">{{ $project->title }}</h2>
                    <p class="zy-muted">Featured workspace</p>
                    <div class="zy-portal-actions" style="margin-top: var(--zy-space-4);">
                        <a href="{{ route('projects.show', $project->slug) }}" class="zy-btn zy-btn--secondary zy-btn--sm">View</a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <div wire:loading.delay class="zy-portal-stack" style="margin-bottom: var(--zy-space-4);">
        <x-ui.skeleton-grid :count="4" />
    </div>

    <div class="zy-portal-project-grid" wire:loading.delay.remove>
        @forelse ($projects as $project)
            @php
                $milestones = $project->milestones;
                $doneCount = $milestones->filter(function ($m) {
                    if (filled($m->completed_at ?? null)) {
                        return true;
                    }
                    $status = $m->status ?? null;

                    return $status instanceof \App\Core\Enums\MilestoneStatus
                        ? $status === \App\Core\Enums\MilestoneStatus::Completed
                        : false;
                })->count();
            @endphp
            <article class="zy-portal-panel zy-portal-project-card zy-portal-panel--lift">
                <div class="zy-portal-project-card__cover">
                    <span class="zy-badge">{{ $project->statusLabel() }}</span>
                </div>
                <div class="zy-portal-row">
                    <div>
                        <h2 class="zy-portal-panel__title">{{ $project->title }}</h2>
                        @if ($project->progressUpdates->isNotEmpty())
                            <p class="zy-muted">Latest: {{ $project->progressUpdates->sortByDesc('created_at')->first()->title ?? 'Update posted' }}</p>
                        @endif
                    </div>
                    <div class="zy-portal-actions">
                        <a href="{{ route('projects.show', $project->slug) }}" class="zy-btn zy-btn--secondary zy-btn--sm">View</a>
                        <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="toggleFavorite('{{ $project->id }}')">Save</button>
                    </div>
                </div>
                @if ($milestones->isNotEmpty())
                    <div class="zy-portal-progress" aria-label="Milestone progress">
                        @foreach ($milestones->take(6) as $milestone)
                            @php
                                $segDone = filled($milestone->completed_at ?? null)
                                    || ($milestone->status ?? null) === \App\Core\Enums\MilestoneStatus::Completed;
                            @endphp
                            <span @class(['zy-portal-progress__seg', 'is-done' => $segDone]) title="{{ $milestone->title }}"></span>
                        @endforeach
                    </div>
                    <ul class="zy-portal-list">
                        @foreach ($milestones->take(4) as $milestone)
                            <li>{{ $milestone->title }}</li>
                        @endforeach
                    </ul>
                @endif
            </article>
        @empty
            <x-ui.empty-state
                class="zy-portal-panel"
                title="No projects linked yet"
                description="Once a quotation is accepted, your project workspace will appear here."
                :lottie="asset('media/lottie/no-connection.lottie')"
            >
                <x-slot:actions>
                    <a href="{{ route('portal.quotations') }}" class="zy-btn zy-btn--primary">View quotations</a>
                </x-slot:actions>
            </x-ui.empty-state>
        @endforelse
    </div>
</div>
