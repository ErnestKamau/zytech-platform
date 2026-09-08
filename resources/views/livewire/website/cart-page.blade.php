<div class="zy-ecom zy-ecom-cart">
    <div class="zy-container">
        <nav class="zy-ecom-crumb" aria-label="Breadcrumb">
            <a href="{{ route('products.index') }}">Products</a>
            <span class="zy-ecom-crumb__sep" aria-hidden="true">/</span>
            <span aria-current="page">Cart</span>
        </nav>

        <header class="zy-ecom-cart__header">
            <div class="zy-ecom-cart__header-copy">
                <p class="zy-ecom-cart__eyebrow">Direct buy</p>
                <h1 class="zy-ecom-cart__title">Your cart</h1>
                <p class="zy-ecom-cart__subtitle">
                    @if ($cart->items->isEmpty())
                        No items yet — add fixed-price catalogue SKUs to get started.
                    @else
                        {{ $totals['item_count'] }} {{ \Illuminate\Support\Str::plural('line', $totals['item_count']) }}
                        · {{ number_format($totals['quantity_total'], 0) }} {{ \Illuminate\Support\Str::plural('unit', (int) $totals['quantity_total']) }}
                    @endif
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="zy-ecom-btn zy-ecom-btn--ghost zy-ecom-cart__header-cta">
                Continue shopping
            </a>
        </header>

        @if ($flash !== '')
            <p class="zy-ecom-cart__flash" role="status">{{ $flash }}</p>
        @endif

        @if ($cart->items->isEmpty())
            <div class="zy-ecom-cart__empty">
                <div class="zy-ecom-cart__empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                </div>
                <h2 class="zy-ecom-cart__empty-title">Cart is empty</h2>
                <p class="zy-ecom-cart__empty-lead">Browse the catalogue and add buyable products. Quote-only SKUs stay on the RFQ path.</p>
                <a href="{{ route('products.index') }}" class="zy-ecom-btn zy-ecom-btn--primary">Browse products</a>
            </div>
        @else
            @php
                $images = config('zyntech-media.images', []);
            @endphp

            <div class="zy-ecom-cart__layout">
                <section class="zy-ecom-cart__panel" aria-label="Cart lines">
                    <div class="zy-ecom-cart__panel-head" aria-hidden="true">
                        <span>Product</span>
                        <span>Qty</span>
                        <span>Total</span>
                    </div>

                    <ul class="zy-ecom-cart__lines" role="list">
                        @foreach ($cart->items as $item)
                            @php
                                $product = $item->product;
                                $imageKey = $product?->image_key;
                                $image = ($imageKey && isset($images[$imageKey])) ? $images[$imageKey] : null;
                                $unit = $product?->unit_of_measure ?: $product?->price_unit;
                            @endphp
                            <li class="zy-ecom-cart__line" wire:key="cart-item-{{ $item->id }}">
                                <div class="zy-ecom-cart__thumb">
                                    @if ($image)
                                        <img src="{{ asset($image['path']) }}" alt="{{ $image['alt'] ?? ($product?->title ?? 'Product') }}" loading="lazy">
                                    @else
                                        <span class="zy-ecom-cart__thumb-fallback" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $product?->icon_path ?: 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z' }}" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>

                                <div class="zy-ecom-cart__line-main">
                                    <p class="zy-ecom-cart__line-title">
                                        @if ($product)
                                            <a href="{{ route('products.show', $product->slug) }}">{{ $product->title }}</a>
                                        @else
                                            Product unavailable
                                        @endif
                                    </p>
                                    <p class="zy-ecom-cart__line-meta">
                                        @if ($product?->sku)
                                            <span>SKU {{ $product->sku }}</span>
                                        @endif
                                        @if ($item->variant)
                                            <span>{{ $item->variant->title }}</span>
                                        @endif
                                    </p>
                                    <p class="zy-ecom-cart__line-price">
                                        {{ $cart->currency }} {{ number_format((float) $item->unit_price, 2) }}
                                        @if ($unit)
                                            <span>/ {{ $unit }}</span>
                                        @endif
                                    </p>
                                    <button
                                        type="button"
                                        class="zy-ecom-cart__remove zy-ecom-cart__remove--mobile"
                                        wire:click="removeItem('{{ $item->id }}')"
                                    >Remove</button>
                                </div>

                                <div class="zy-ecom-cart__qty-wrap">
                                    <span class="zy-ecom-cart__field-label">Qty</span>
                                    <div class="zy-ecom-qty__control">
                                        <button
                                            type="button"
                                            wire:click="updateQuantity('{{ $item->id }}', {{ max(0, (float) $item->quantity - 1) }})"
                                            aria-label="Decrease quantity"
                                        >−</button>
                                        <span class="zy-ecom-cart__qty-value" aria-label="Quantity">{{ number_format((float) $item->quantity, 0) }}</span>
                                        <button
                                            type="button"
                                            wire:click="updateQuantity('{{ $item->id }}', {{ (float) $item->quantity + 1 }})"
                                            aria-label="Increase quantity"
                                        >+</button>
                                    </div>
                                </div>

                                <div class="zy-ecom-cart__line-end">
                                    <p class="zy-ecom-cart__line-total">
                                        <span class="zy-ecom-cart__field-label">Total</span>
                                        {{ $cart->currency }} {{ number_format((float) $item->unit_price * (float) $item->quantity, 2) }}
                                    </p>
                                    <button
                                        type="button"
                                        class="zy-ecom-cart__remove zy-ecom-cart__remove--desktop"
                                        wire:click="removeItem('{{ $item->id }}')"
                                    >Remove</button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <aside class="zy-ecom-cart__summary" aria-label="Order summary">
                    <div class="zy-ecom-cart__summary-top">
                        <h2 class="zy-ecom-cart__summary-title">Order summary</h2>
                        <p class="zy-ecom-cart__summary-meta">
                            {{ number_format($totals['quantity_total'], 0) }} {{ \Illuminate\Support\Str::plural('unit', (int) $totals['quantity_total']) }}
                        </p>
                    </div>

                    <dl class="zy-ecom-cart__summary-rows">
                        <div>
                            <dt>Subtotal</dt>
                            <dd>{{ $cart->currency }} {{ number_format($totals['subtotal'], 2) }}</dd>
                        </div>
                        <div class="zy-ecom-cart__summary-note-row">
                            <dt>Tax &amp; shipping</dt>
                            <dd>At checkout</dd>
                        </div>
                        <div class="zy-ecom-cart__summary-total">
                            <dt>Estimated total</dt>
                            <dd>{{ $cart->currency }} {{ number_format($totals['subtotal'], 2) }}</dd>
                        </div>
                    </dl>

                    <div class="zy-ecom-cart__summary-actions">
                        @if ($canCheckout)
                            <a href="{{ route('checkout') }}" class="zy-ecom-btn zy-ecom-btn--primary zy-ecom-btn--block">
                                Proceed to checkout
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="zy-ecom-btn zy-ecom-btn--primary zy-ecom-btn--block">
                                Sign in to checkout
                            </a>
                            <p class="zy-ecom-cart__hint">
                                Checkout needs a client account so the order is tied to your company profile.
                            </p>
                        @endif

                        <button type="button" class="zy-ecom-btn zy-ecom-btn--ghost zy-ecom-btn--block" wire:click="clear" wire:confirm="Clear all items from this cart?">
                            Clear cart
                        </button>
                    </div>

                    <ul class="zy-ecom-cart__trust" role="list">
                        <li>Prices locked at add-to-cart time until checkout.</li>
                        <li>Quote-only products cannot enter this cart.</li>
                    </ul>
                </aside>
            </div>
        @endif
    </div>
</div>
