<div class="zy-ecom zy-ecom-cart zy-ecom-checkout">
    <div class="zy-container">
        <nav class="zy-ecom-crumb" aria-label="Breadcrumb">
            <a href="{{ route('cart') }}">Cart</a>
            <span class="zy-ecom-crumb__sep" aria-hidden="true">/</span>
            <span aria-current="page">Checkout</span>
        </nav>

        <header class="zy-ecom-cart__header">
            <h1 class="zy-ecom-cart__title">Checkout</h1>
            <p class="zy-ecom-cart__subtitle">Confirm contact and fulfillment details for your direct-buy order.</p>
        </header>

        @if ($error !== '')
            <p class="zy-ecom-cart__flash is-error" role="alert">{{ $error }}</p>
        @endif

        <div class="zy-ecom-cart__layout">
            <form class="zy-ecom-checkout__form" wire:submit="placeOrder">
                <fieldset class="zy-ecom-checkout__fieldset">
                    <legend>Fulfillment</legend>
                    <label class="zy-ecom-checkout__choice">
                        <input type="radio" wire:model.live="fulfillment_method" value="delivery">
                        Delivery
                    </label>
                    <label class="zy-ecom-checkout__choice">
                        <input type="radio" wire:model.live="fulfillment_method" value="pickup">
                        Pickup
                    </label>
                </fieldset>

                <fieldset class="zy-ecom-checkout__fieldset">
                    <legend>Contact</legend>
                    <label>
                        <span>Name</span>
                        <input type="text" wire:model="contact_name" required>
                        @error('contact_name') <span class="zy-ecom-checkout__error">{{ $message }}</span> @enderror
                    </label>
                    <label>
                        <span>Email</span>
                        <input type="email" wire:model="contact_email" required>
                        @error('contact_email') <span class="zy-ecom-checkout__error">{{ $message }}</span> @enderror
                    </label>
                    <label>
                        <span>Phone</span>
                        <input type="text" wire:model="contact_phone">
                    </label>
                </fieldset>

                <fieldset class="zy-ecom-checkout__fieldset">
                    <legend>Addresses</legend>
                    <label>
                        <span>Billing address</span>
                        <textarea rows="3" wire:model="billing_address"></textarea>
                    </label>
                    @if ($fulfillment_method === 'delivery')
                        <label>
                            <span>Delivery address</span>
                            <textarea rows="3" wire:model="delivery_address"></textarea>
                        </label>
                    @endif
                    <label>
                        <span>Notes</span>
                        <textarea rows="3" wire:model="notes"></textarea>
                    </label>
                </fieldset>

                <button type="submit" class="zy-ecom-btn zy-ecom-btn--primary zy-ecom-btn--block" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="placeOrder">Place order</span>
                    <span wire:loading wire:target="placeOrder">Placing…</span>
                </button>
            </form>

            <aside class="zy-ecom-cart__summary">
                <h2 class="zy-ecom-cart__summary-title">Order summary</h2>
                <ul class="zy-ecom-checkout__lines" role="list">
                    @foreach ($cart->items as $item)
                        <li>
                            <span>{{ $item->product?->title ?? 'Product' }} × {{ number_format((float) $item->quantity, 0) }}</span>
                            <strong>{{ $cart->currency }} {{ number_format((float) $item->unit_price * (float) $item->quantity, 2) }}</strong>
                        </li>
                    @endforeach
                </ul>
                <dl class="zy-ecom-cart__summary-rows">
                    <div>
                        <dt>Subtotal</dt>
                        <dd>{{ $cart->currency }} {{ number_format($totals['subtotal'], 2) }}</dd>
                    </div>
                </dl>
                <a href="{{ route('cart') }}" class="zy-ecom-cart__continue">Back to cart</a>
            </aside>
        </div>
    </div>
</div>
