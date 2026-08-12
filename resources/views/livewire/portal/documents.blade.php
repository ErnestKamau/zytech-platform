<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <h1 class="zy-portal-page__title">Documents</h1>
    </header>

    <div class="zy-portal-stack">
        @forelse ($documents as $document)
            <article class="zy-portal-panel">
                <div class="zy-portal-row">
                    <div>
                        <strong>{{ $document->title }}</strong>
                        <p class="zy-muted">{{ $document->kind }} · {{ $document->created_at?->toFormattedDateString() }}</p>
                    </div>
                    <button type="button" class="zy-btn zy-btn--secondary zy-btn--sm" wire:click="download('{{ $document->id }}')">Download</button>
                </div>
            </article>
        @empty
            <p class="zy-muted">No client-visible documents are available yet.</p>
        @endforelse
    </div>
</div>
