<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <div class="zy-portal-page__intro">
            <p class="zy-section__eyebrow">Files</p>
            <h1 class="zy-portal-page__title">Documents</h1>
            <p class="zy-portal-page__lead">Download contracts, drawings, and other files shared with your account.</p>
        </div>
    </header>

    <div class="zy-portal-stack">
        @forelse ($documents as $document)
            <article class="zy-portal-panel">
                <div class="zy-portal-row">
                    <div>
                        <p class="zy-eyebrow">{{ $document->kind }}</p>
                        <h2 class="zy-portal-panel__title">{{ $document->title }}</h2>
                        <p class="zy-muted">{{ $document->created_at?->toFormattedDateString() }}</p>
                    </div>
                    <div class="zy-portal-actions">
                        <button type="button" class="zy-btn zy-btn--secondary zy-btn--sm" wire:click="download('{{ $document->id }}')">Download</button>
                    </div>
                </div>
            </article>
        @empty
            <div class="zy-portal-empty">
                <p class="zy-section__eyebrow">Shared files</p>
                <p>No client-visible documents are available yet. We will notify you when new files are posted.</p>
            </div>
        @endforelse
    </div>
</div>
