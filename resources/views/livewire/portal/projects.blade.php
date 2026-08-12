<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <h1 class="zy-portal-page__title">Projects</h1>
    </header>

    <div class="zy-portal-stack">
        @forelse ($projects as $project)
            <article class="zy-portal-panel">
                <div class="zy-portal-row">
                    <div>
                        <strong>{{ $project->title }}</strong>
                        <p class="zy-muted">{{ $project->statusLabel() }}</p>
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
            <p class="zy-muted">No projects linked yet. Once a quotation is accepted, your project workspace will appear here.</p>
        @endforelse
    </div>
</div>
