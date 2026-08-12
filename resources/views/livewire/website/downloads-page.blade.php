<div class="zy-page-downloads">
    <section class="zy-section">
        <div class="zy-container">
            <div class="zy-section__header">
                <p class="zy-section__eyebrow">Resources</p>
                <h1>Downloads</h1>
                <p>Public guides and attachments published with Knowledge Centre articles.</p>
            </div>

            <div class="zy-stack">
                @forelse ($downloads as $download)
                    <article class="zy-download-row">
                        <div>
                            <h2>{{ $download->title }}</h2>
                            @if ($download->description)
                                <p class="zy-muted">{{ $download->description }}</p>
                            @endif
                            <p class="zy-muted">From <a href="{{ route('knowledge.show', $download->article->slug) }}">{{ $download->article->title }}</a></p>
                        </div>
                        @if (filled($download->external_url))
                            <a href="{{ $download->external_url }}" class="zy-btn zy-btn--secondary zy-btn--sm" target="_blank" rel="noopener">Open</a>
                        @elseif (filled($download->file_key))
                            <span class="zy-badge">{{ $download->file_key }}</span>
                        @endif
                    </article>
                @empty
                    <p class="zy-muted">No public downloads are published yet. Browse the <a href="{{ route('knowledge.index') }}">Knowledge Centre</a> for guides.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
