@props(['product'])

@php
    $images = config('zyntech-media.images', []);
    $image = ($product->imageKey && isset($images[$product->imageKey]))
        ? $images[$product->imageKey]
        : null;
    $uploadedIcon = $product->iconUrl();
    $modeLabel = match ($product->purchaseMode->value) {
        'buy' => 'Buy',
        'both' => 'Buy / Quote',
        default => 'Quote',
    };
@endphp

<a href="{{ route('products.show', $product->slug) }}" class="zy-ecom-card" id="{{ $product->slug }}">
    <div class="zy-ecom-card__media">
        @if ($product->isFeatured)
            <span class="zy-ecom-card__badge">Featured</span>
        @endif
        <span class="zy-ecom-card__mode">{{ $modeLabel }}</span>
        @if ($image)
            <img src="{{ asset($image['path']) }}" alt="{{ $image['alt'] ?? $product->title }}" loading="lazy">
        @elseif ($uploadedIcon)
            <img src="{{ $uploadedIcon }}" alt="{{ $product->title }}" loading="lazy">
        @else
            <div class="zy-ecom-card__media-fallback" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </div>
        @endif
    </div>
    <div class="zy-ecom-card__body">
        <p class="zy-ecom-card__cat">{{ $product->categoryName }}</p>
        <p class="zy-ecom-card__title">{{ $product->title }}</p>
        @if ($product->excerpt)
            <p class="zy-ecom-card__excerpt">{{ $product->excerpt }}</p>
        @endif
        <div class="zy-ecom-card__footer">
            @if ($product->priceAmount)
                <p class="zy-ecom-card__price">
                    {{ $product->priceCurrency }} {{ number_format((float) $product->priceAmount, 0) }}
                    @if ($product->priceUnit || $product->unitOfMeasure)
                        <span>/ {{ $product->priceUnit ?: $product->unitOfMeasure }}</span>
                    @endif
                </p>
            @else
                <p class="zy-ecom-card__cta">Request quote</p>
            @endif
            @if ($product->sku)
                <p class="zy-ecom-card__sku">{{ $product->sku }}</p>
            @endif
        </div>
    </div>
</a>
