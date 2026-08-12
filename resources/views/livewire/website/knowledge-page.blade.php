@php
    $images = config('zyntech-media.images');
    $heroImage = $images['site_prep_ballast'];
@endphp

<div>
    <div class="zy-container zy-knowledge-intro">
        <x-media.banner :src="asset($heroImage['path'])" :alt="$heroImage['alt']">
            <p class="zy-eyebrow" style="color: rgb(255 255 255 / 0.75);">Knowledge Centre</p>
            <h1 style="color: #fff;">{{ $selectedCategory?->name ?? 'Knowledge Centre' }}</h1>
            <p>{{ $selectedCategory?->description ?: 'Practical construction guides for Kenyan homeowners, developers, and site teams — written from real Zytech projects.' }}</p>
        </x-media.banner>
    </div>

    <div class="zy-container zy-knowledge-search">
        <form wire:submit.prevent class="zy-knowledge-search__form">
            <label for="knowledge-search" class="sr-only">Search articles</label>
            <input
                id="knowledge-search"
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Search guides, regulations, and cost topics…"
                class="zy-input"
            >
        </form>
    </div>

    @if ($categories->isNotEmpty())
        <nav class="zy-container zy-knowledge-cats" aria-label="Article categories">
            <a href="{{ route('knowledge.index') }}" class="zy-knowledge-cat {{ $selectedCategory === null ? 'is-active' : '' }}">All</a>
            @foreach ($categories as $item)
                <a
                    href="{{ route('knowledge.category', $item->slug) }}"
                    class="zy-knowledge-cat {{ $selectedCategory?->slug === $item->slug ? 'is-active' : '' }}"
                >{{ $item->name }}</a>
            @endforeach
        </nav>
    @endif

    @if ($selectedCategory === null && $search === '')
        <livewire:knowledge.featured-articles />
    @endif

    <div class="zy-container zy-knowledge-grid">
        <div wire:loading.delay.short wire:target="search">
            <x-ui.skeleton-grid :count="6" />
        </div>

        <div wire:loading.remove.delay.short wire:target="search">
            @if ($articles->isEmpty())
                <x-ui.empty-state
                    title="No articles match"
                    description="No published guides match your search yet. Browse all categories or request a quote for project-specific advice."
                    :lottie="asset('media/lottie/no-connection.lottie')"
                >
                    <x-slot:actions>
                        <a href="{{ route('knowledge.index') }}" class="zy-btn zy-btn--primary">All articles</a>
                        <a href="{{ route('quote.index') }}" class="zy-btn zy-btn--secondary">Request a quote</a>
                    </x-slot:actions>
                </x-ui.empty-state>
            @else
                <div class="zy-grid zy-grid--3">
                    @foreach ($articles as $article)
                        <x-knowledge.card :article="$article" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
