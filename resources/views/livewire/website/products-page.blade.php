<div class="zy-ecom">
    <div class="zy-container">
        <header class="zy-ecom-hero">
            <p class="zy-ecom-hero__eyebrow">Zyntech Commerce</p>
            <h1 class="zy-ecom-hero__title">{{ $selectedCategory?->name ?? 'Materials. Specs. Supply.' }}</h1>
            <p class="zy-ecom-hero__lead">
                {{ $selectedCategory?->description ?: 'Construction products with known pricing for direct purchase, or request a quote when quantities, logistics, or project terms need negotiation.' }}
            </p>
            <div class="zy-ecom-hero__meta">
                <span class="zy-ecom-chip zy-ecom-chip--live">Catalogue live</span>
                <span class="zy-ecom-chip">{{ $products->count() }} SKU{{ $products->count() === 1 ? '' : 's' }} showing</span>
                <span class="zy-ecom-chip">Buy · Quote · Project supply</span>
            </div>
        </header>

        <div class="zy-ecom-shell">
            @if ($categories->isNotEmpty())
                <aside class="zy-ecom-rail" aria-label="Product categories">
                    <p class="zy-ecom-rail__label">Categories</p>
                    <a
                        href="{{ route('products.index') }}"
                        class="zy-ecom-rail__link {{ $selectedCategory === null ? 'is-active' : '' }}"
                    >
                        <span>All products</span>
                    </a>
                    @foreach ($categories as $item)
                        <a
                            href="{{ route('products.category', $item->slug) }}"
                            class="zy-ecom-rail__link {{ $selectedCategory?->slug === $item->slug ? 'is-active' : '' }}"
                        >
                            <span>{{ $item->name }}</span>
                            @if (isset($item->published_products_count))
                                <span class="zy-ecom-rail__count">{{ $item->published_products_count }}</span>
                            @endif
                        </a>
                    @endforeach
                </aside>
            @endif

            <div>
                @if ($featured->isNotEmpty())
                    <section class="zy-ecom-featured" aria-label="Featured products">
                        <div class="zy-ecom-featured__head">
                            <div>
                                <p class="zy-ecom-featured__eyebrow">Signal SKUs</p>
                                <h2 class="zy-ecom-featured__title">High-demand materials</h2>
                            </div>
                        </div>
                        <div class="zy-ecom-featured__grid">
                            @foreach ($featured as $item)
                                <x-products.card :product="$item" />
                            @endforeach
                        </div>
                    </section>
                @endif

                <div class="zy-ecom-toolbar">
                    <p class="zy-ecom-toolbar__count">{{ $products->count() }} result{{ $products->count() === 1 ? '' : 's' }}</p>
                    <div class="zy-ecom-search">
                        <svg class="zy-ecom-search__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.6-5.4a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <label class="zy-sr-only" for="ecom-search">Search products</label>
                        <input
                            id="ecom-search"
                            type="search"
                            wire:model.live.debounce.250ms="search"
                            placeholder="Search name, SKU, category…"
                        >
                    </div>
                    <label class="zy-sr-only" for="ecom-sort">Sort products</label>
                    <select id="ecom-sort" class="zy-ecom-sort" wire:model.live="sort">
                        <option value="featured">Featured first</option>
                        <option value="name">Name A–Z</option>
                        <option value="price_asc">Price ↑</option>
                        <option value="price_desc">Price ↓</option>
                    </select>
                </div>

                @if ($products->isEmpty())
                    <div class="zy-ecom-empty">
                        <h2>No matching products</h2>
                        <p>Try another category or clear the search. You can still open a custom quote for project supply.</p>
                        <div class="zy-ecom-empty__actions">
                            <a href="{{ route('products.index') }}" class="zy-ecom-btn zy-ecom-btn--primary">Reset catalogue</a>
                            <a href="{{ route('quote.index') }}" class="zy-ecom-btn zy-ecom-btn--ghost">Request a quote</a>
                        </div>
                    </div>
                @else
                    <div class="zy-ecom-grid" wire:loading.class="is-loading">
                        @foreach ($products as $product)
                            <x-products.card :product="$product" />
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
