@if ($products->isNotEmpty())
    <section class="zy-ecom-featured" aria-label="Featured products">
        <div class="zy-ecom-featured__head">
            <div>
                <p class="zy-ecom-featured__eyebrow">Signal SKUs</p>
                <h2 class="zy-ecom-featured__title">High-demand materials</h2>
            </div>
        </div>
        <div class="zy-ecom-featured__grid">
            @foreach ($products as $product)
                <x-products.card :product="$product" />
            @endforeach
        </div>
    </section>
@endif
