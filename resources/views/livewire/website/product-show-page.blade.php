@php
    $images = config('zyntech-media.images', []);
    $galleryKeys = $product->galleryKeys ?: array_filter([$product->imageKey]);
    $gallery = collect($galleryKeys)
        ->map(fn (string $key) => isset($images[$key]) ? ['key' => $key, ...$images[$key]] : null)
        ->filter()
        ->values();

    if ($gallery->isEmpty() && $product->imageKey && isset($images[$product->imageKey])) {
        $gallery = collect([['key' => $product->imageKey, ...$images[$product->imageKey]]]);
    }

    $active = $gallery->get($activeImage) ?? $gallery->first();
    $stock = $product->stockDisplay;
    $stockClass = 'zy-ecom-stock--request';
    $stockLabel = 'Available on request';
    if ($stock !== null) {
        if ($stock <= 0) {
            $stockClass = 'zy-ecom-stock--out';
            $stockLabel = 'Out of stock';
        } elseif ($stock <= 25) {
            $stockClass = 'zy-ecom-stock--low';
            $stockLabel = 'Low stock · '.$stock.' available';
        } else {
            $stockClass = '';
            $stockLabel = 'In stock · '.$stock.' available';
        }
    }
@endphp

<div class="zy-ecom zy-ecom-pdp">
    <div class="zy-container">
        <nav class="zy-ecom-crumb" aria-label="Breadcrumb">
            <a href="{{ route('products.index') }}">Products</a>
            <span class="zy-ecom-crumb__sep" aria-hidden="true">/</span>
            @if ($product->categorySlug)
                <a href="{{ route('products.category', $product->categorySlug) }}">{{ $product->categoryName }}</a>
                <span class="zy-ecom-crumb__sep" aria-hidden="true">/</span>
            @endif
            <span aria-current="page">{{ $product->title }}</span>
        </nav>

        <div class="zy-ecom-pdp__layout">
            <div class="zy-ecom-gallery">
                <div class="zy-ecom-gallery__stage">
                    @if ($active)
                        <img src="{{ asset($active['path']) }}" alt="{{ $active['alt'] ?? $product->title }}">
                    @else
                        <div class="zy-ecom-gallery__fallback" aria-hidden="true">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $product->iconPath ?: 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z' }}" />
                            </svg>
                        </div>
                    @endif
                </div>
                @if ($gallery->count() > 1)
                    <div class="zy-ecom-gallery__thumbs" role="tablist" aria-label="Product images">
                        @foreach ($gallery as $index => $thumb)
                            <button
                                type="button"
                                class="zy-ecom-gallery__thumb {{ $activeImage === $index ? 'is-active' : '' }}"
                                wire:click="setImage({{ $index }})"
                                aria-label="Show image {{ $index + 1 }}"
                                aria-selected="{{ $activeImage === $index ? 'true' : 'false' }}"
                            >
                                <img src="{{ asset($thumb['path']) }}" alt="" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="zy-ecom-buy">
                <p class="zy-ecom-buy__brand">{{ $product->categoryName ?: 'Zyntech Supply' }}</p>
                <h1 class="zy-ecom-buy__title">{{ $product->title }}</h1>
                @if ($product->sku)
                    <p class="zy-ecom-buy__sku">SKU {{ $product->sku }}</p>
                @endif

                <div class="zy-ecom-buy__price-row">
                    @if ($product->priceAmount)
                        <p class="zy-ecom-buy__price">
                            {{ $product->priceCurrency }} {{ number_format((float) $product->priceAmount, 2) }}
                        </p>
                        @if ($product->priceUnit || $product->unitOfMeasure)
                            <span class="zy-ecom-buy__unit">/ {{ $product->priceUnit ?: $product->unitOfMeasure }}</span>
                        @endif
                    @else
                        <p class="zy-ecom-buy__price">Quote pricing</p>
                    @endif
                    <p class="zy-ecom-buy__model">{{ $product->pricingModel->label() }}</p>
                </div>

                <p class="zy-ecom-stock {{ $stockClass }}">{{ $stockLabel }}</p>

                @if ($variants->isNotEmpty())
                    <div class="zy-ecom-qty" style="margin-bottom: 0.85rem;">
                        <span class="zy-ecom-qty__label">Variant</span>
                        <div class="zy-ecom-buy__actions" style="gap: 0.4rem;">
                            @foreach ($variants as $variant)
                                <button
                                    type="button"
                                    class="zy-ecom-btn zy-ecom-btn--ghost zy-ecom-btn--block {{ $selectedVariantId === $variant->id ? 'is-active' : '' }}"
                                    wire:click="selectVariant('{{ $variant->id }}')"
                                >
                                    {{ $variant->title }}
                                    @if ($variant->price_amount)
                                        · {{ $product->priceCurrency }} {{ number_format((float) $variant->price_amount, 2) }}
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($product->excerpt)
                    <p class="zy-ecom-buy__note">{{ $product->excerpt }}</p>
                @endif

                <div class="zy-ecom-qty">
                    <span class="zy-ecom-qty__label">Quantity</span>
                    <div class="zy-ecom-qty__control">
                        <button type="button" wire:click="decrementQty" aria-label="Decrease quantity">−</button>
                        <input type="number" min="1" max="999" wire:model.live="quantity" aria-label="Quantity">
                        <button type="button" wire:click="incrementQty" aria-label="Increase quantity">+</button>
                    </div>
                </div>

                <div class="zy-ecom-buy__actions">
                    @if ($product->purchaseMode->allowsBuy())
                        <button
                            type="button"
                            class="zy-ecom-btn zy-ecom-btn--primary zy-ecom-btn--block"
                            wire:click="addToCart"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="addToCart">Add to cart</span>
                            <span wire:loading wire:target="addToCart">Adding…</span>
                        </button>
                    @endif
                    @if ($product->purchaseMode->allowsQuote())
                        <a
                            href="{{ route('quote.index', ['product' => $product->slug]) }}"
                            class="zy-ecom-btn {{ $product->purchaseMode->allowsBuy() ? 'zy-ecom-btn--ghost' : 'zy-ecom-btn--primary' }} zy-ecom-btn--block"
                        >
                            Request quote · qty {{ $quantity }}
                        </a>
                    @endif
                    <button type="button" class="zy-ecom-btn zy-ecom-btn--steel zy-ecom-btn--block" disabled aria-disabled="true">
                        Save to wishlist — soon
                    </button>
                </div>

                @if ($cartMessage !== '')
                    <p class="zy-ecom-buy__flash {{ $cartMessageError ? 'is-error' : '' }}" role="status">
                        {{ $cartMessage }}
                        @unless ($cartMessageError)
                            <a href="{{ route('cart') }}">View cart</a>
                        @endunless
                    </p>
                @endif

                @if ($product->pricingNotes)
                    <p class="zy-ecom-buy__note">{{ $product->pricingNotes }}</p>
                @endif

                <div class="zy-ecom-trust">
                    <p class="zy-ecom-trust__item"><strong>Dual path</strong> — buy fixed-price SKUs or negotiate via RFQ.</p>
                    <p class="zy-ecom-trust__item"><strong>Specs first</strong> — technical data sits beside commercial terms.</p>
                    <p class="zy-ecom-trust__item"><strong>Project ready</strong> — portal quotes, orders, and invoices when accepted.</p>
                </div>
            </aside>
        </div>

        <section class="zy-ecom-detail" aria-label="Product information">
            <div class="zy-ecom-tabs" role="tablist">
                <button type="button" class="{{ $tab === 'overview' ? 'is-active' : '' }}" wire:click="setTab('overview')" role="tab" aria-selected="{{ $tab === 'overview' ? 'true' : 'false' }}">Overview</button>
                <button type="button" class="{{ $tab === 'specifications' ? 'is-active' : '' }}" wire:click="setTab('specifications')" role="tab" aria-selected="{{ $tab === 'specifications' ? 'true' : 'false' }}">Specifications</button>
                <button type="button" class="{{ $tab === 'documents' ? 'is-active' : '' }}" wire:click="setTab('documents')" role="tab" aria-selected="{{ $tab === 'documents' ? 'true' : 'false' }}">Documents</button>
            </div>

            <div class="zy-ecom-panel {{ $tab === 'overview' ? 'is-active' : '' }}" role="tabpanel">
                <p>{{ $product->body ?: $product->excerpt ?: 'Technical overview will appear here as product content is completed.' }}</p>
            </div>

            <div class="zy-ecom-panel {{ $tab === 'specifications' ? 'is-active' : '' }}" role="tabpanel">
                @if (! empty($product->specifications))
                    <dl class="zy-ecom-specs">
                        @foreach ($product->specifications as $label => $value)
                            <div>
                                <dt>{{ $label }}</dt>
                                <dd>{{ $value }}</dd>
                            </div>
                        @endforeach
                        @if ($product->unitOfMeasure)
                            <div>
                                <dt>Unit of measure</dt>
                                <dd>{{ $product->unitOfMeasure }}</dd>
                            </div>
                        @endif
                        @if ($product->taxable)
                            <div>
                                <dt>Tax</dt>
                                <dd>Taxable</dd>
                            </div>
                        @endif
                    </dl>
                @else
                    <p>No structured specifications published for this SKU yet.</p>
                @endif
            </div>

            <div class="zy-ecom-panel {{ $tab === 'documents' ? 'is-active' : '' }}" role="tabpanel">
                <p>Datasheets, certificates, and installation documents will attach here. For project packs, request a quote and our team will include the relevant files.</p>
            </div>
        </section>

        @if ($related->isNotEmpty())
            <section class="zy-ecom-related" aria-label="Related products">
                <div class="zy-ecom-related__head">
                    <div>
                        <p class="zy-ecom-related__eyebrow">Compatible line</p>
                        <h2 class="zy-ecom-related__title">Related in {{ $product->categoryName }}</h2>
                    </div>
                    <a href="{{ route('products.category', $product->categorySlug) }}" class="zy-ecom-btn zy-ecom-btn--ghost">View category</a>
                </div>
                <div class="zy-ecom-grid">
                    @foreach ($related as $item)
                        <x-products.card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
