<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <div class="zy-portal-page__intro">
            <p class="zy-section__eyebrow">Delivery</p>
            <h1 class="zy-portal-page__title">Projects</h1>
            <p class="zy-portal-page__lead">Track linked projects, milestones, and the latest progress updates from the Zytech team.</p>
        </div>
    </header>

    <div class="zy-portal-stack">
        @forelse ($projects as $project)
            <article class="zy-portal-panel">
                <div class="zy-portal-row">
                    <div>
                        <p class="zy-eyebrow">{{ $project->statusLabel() }}</p>
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
                @if ($project->milestones->isNotEmpty())
                    <ul class="zy-portal-list">
                        @foreach ($project->milestones->take(4) as $milestone)
                            <li>{{ $milestone->title }}</li>
                        @endforeach
                    </ul>
                @endif
            </article>
        @empty
            <div class="zy-portal-empty">
                <p class="zy-section__eyebrow">In progress</p>
                <p>No projects linked yet. Once a quotation is accepted, your project workspace will appear here.</p>
            </div>
        @endforelse
    </div>
</div>
