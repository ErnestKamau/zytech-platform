<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Files"
        title="Documents"
        lead="Download contracts, drawings, and other files shared with your account — or upload your own."
        icon="inbox"
    />

    <x-portal.list-toolbar
        search-model="search"
        filter-model="kind"
        :filter-options="$kindOptions"
        filter-label="Kind"
        placeholder="Search documents…"
        export-action="export"
    />

    <div class="zy-portal-upload">
        <div class="zy-portal-panel__title-wrap">
            <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="upload" /></span>
            <h2 class="zy-portal-panel__title">Upload a file</h2>
        </div>
        <form wire:submit="upload" class="zy-portal-upload__grid">
            <div class="zy-field">
                <label class="zy-label" for="doc-title">Title</label>
                <input id="doc-title" type="text" class="zy-input" wire:model="uploadTitle" placeholder="Contract addendum">
                @error('uploadTitle') <p class="zy-field-error">{{ $message }}</p> @enderror
            </div>
            <div class="zy-field">
                <label class="zy-label" for="doc-file">File</label>
                <input id="doc-file" type="file" class="zy-input" wire:model="uploadFile">
                @error('uploadFile') <p class="zy-field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <button type="submit" class="zy-btn zy-btn--primary zy-btn--sm" wire:loading.attr="disabled">
                    <x-portal.icon name="upload" />
                    Upload
                </button>
            </div>
        </form>
    </div>

    <div wire:loading.delay class="zy-portal-stack" style="margin-bottom: var(--zy-space-4);">
        <x-ui.skeleton-grid :count="4" />
    </div>

    <div class="zy-portal-doc-grid" wire:loading.delay.remove>
        @forelse ($documents as $document)
            <article class="zy-portal-panel zy-portal-doc-card">
                <div class="zy-portal-doc-card__top">
                    <span class="zy-portal-panel__icon" aria-hidden="true">
                        <x-portal.icon name="document" />
                    </span>
                    <span class="zy-badge zy-badge--neutral">{{ $document->kind }}</span>
                </div>
                <div>
                    <h2 class="zy-portal-panel__title">{{ $document->title }}</h2>
                    <p class="zy-muted">{{ $document->created_at?->toFormattedDateString() }}</p>
                </div>
                <div class="zy-portal-actions">
                    <a href="{{ route('portal.documents.download', $document) }}" class="zy-btn zy-btn--secondary zy-btn--sm">
                        <x-portal.icon name="download" />
                        Download
                    </a>
                </div>
            </article>
        @empty
            <x-ui.empty-state
                class="zy-portal-panel"
                title="No documents yet"
                description="Contracts, drawings, and shared files will show up here when your team posts them — or upload one above."
                :lottie="asset('media/lottie/no-connection.lottie')"
            >
                <x-slot:actions>
                    <a href="{{ route('portal.support') }}" class="zy-btn zy-btn--primary">Contact support</a>
                </x-slot:actions>
            </x-ui.empty-state>
        @endforelse
    </div>
</div>
