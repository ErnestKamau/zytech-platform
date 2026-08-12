<div class="zy-page-search">
    <div class="zy-container zy-section">
        <header class="zy-section__header">
            <h1>Search</h1>
            <p class="zy-muted">Find projects, services, and knowledge articles.</p>
        </header>

        <form class="zy-form zy-search-form" wire:submit.prevent>
            <label class="zy-sr-only" for="site-search">Search</label>
            <input
                id="site-search"
                type="search"
                wire:model.live.debounce.300ms="q"
                placeholder="Search Zytech…"
                autocomplete="off"
            >
        </form>

        @if ($q === '')
            <section class="zy-search-popular">
                <h2>Popular searches</h2>
                <div class="zy-search-chips">
                    @forelse ($popular as $term)
                        <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="$set('q', '{{ $term }}')">{{ $term }}</button>
                    @empty
                        <p class="zy-muted">Start typing to search the catalogue.</p>
                    @endforelse
                </div>
            </section>
        @else
            @if ($suggestions !== [])
                <div class="zy-search-chips">
                    @foreach ($suggestions as $term)
                        <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="$set('q', '{{ $term }}')">{{ $term }}</button>
                    @endforeach
                </div>
            @endif

            <section class="zy-search-results">
                <div wire:loading.delay.short wire:target="q">
                    <x-ui.skeleton-grid :count="3" />
                </div>

                <div wire:loading.remove.delay.short wire:target="q">
                    <h2>{{ $results->count() }} result{{ $results->count() === 1 ? '' : 's' }}</h2>
                    @forelse ($results as $result)
                        <article class="zy-search-result">
                            <span class="zy-badge">{{ ucfirst($result->type) }}</span>
                            <h3><a href="{{ $result->url }}">{{ $result->title }}</a></h3>
                            @if ($result->excerpt)
                                <p class="zy-muted">{{ \Illuminate\Support\Str::limit($result->excerpt, 160) }}</p>
                            @endif
                        </article>
                    @empty
                        <x-ui.empty-state
                            title="No matches for “{{ $q }}”"
                            description="Try a different keyword, or browse services and projects directly."
                            :lottie="asset('media/lottie/no-connection.lottie')"
                        >
                            <x-slot:actions>
                                <a href="{{ route('services.index') }}" class="zy-btn zy-btn--primary">Services</a>
                                <a href="{{ route('projects.index') }}" class="zy-btn zy-btn--secondary">Projects</a>
                            </x-slot:actions>
                        </x-ui.empty-state>
                    @endforelse
                </div>
            </section>
        @endif
    </div>
</div>
