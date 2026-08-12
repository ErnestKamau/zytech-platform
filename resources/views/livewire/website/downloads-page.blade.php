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
                    <x-ui.empty-state
                        title="No downloads yet"
                        description="Public guides and attachments will appear here when published with Knowledge Centre articles."
                        :lottie="asset('media/lottie/no-connection.lottie')"
                    >
                        <x-slot:actions>
                            <a href="{{ route('knowledge.index') }}" class="zy-btn zy-btn--primary">Browse Knowledge Centre</a>
                        </x-slot:actions>
                    </x-ui.empty-state>
                @endforelse
            </div>
        </div>
    </section>
</div>
