<div class="zy-ecom zy-ecom-cart">
    <div class="zy-container">
        <header class="zy-ecom-cart__header">
            <h1 class="zy-ecom-cart__title">Order placed</h1>
            <p class="zy-ecom-cart__subtitle">
                Reference <strong>{{ $order->order_number }}</strong>
                · {{ $order->status->label() }}
            </p>
        </header>

        <div class="zy-ecom-cart__summary" style="max-width: 32rem;">
            <dl class="zy-ecom-cart__summary-rows">
                <div>
                    <dt>Total</dt>
                    <dd>{{ $order->currency }} {{ number_format((float) $order->total_amount, 2) }}</dd>
                </div>
                <div>
                    <dt>Payment</dt>
                    <dd>{{ $order->payment_status->label() }}</dd>
                </div>
                <div>
                    <dt>Fulfillment</dt>
                    <dd>{{ $order->fulfillment_method->label() }}</dd>
                </div>
            </dl>

            <ul class="zy-ecom-checkout__lines" role="list">
                @foreach ($order->items as $item)
                    <li>
                        <span>{{ $item->product_title_snapshot }} × {{ number_format((float) $item->quantity, 0) }}</span>
                        <strong>{{ $order->currency }} {{ number_format((float) $item->line_total, 2) }}</strong>
                    </li>
                @endforeach
            </ul>

            @auth
                @if (auth()->user()?->clientProfile?->portal_access_granted_at)
                    <a href="{{ route('portal.orders') }}" class="zy-ecom-btn zy-ecom-btn--primary zy-ecom-btn--block">View in portal</a>
                @endif
            @endauth
            <a href="{{ route('products.index') }}" class="zy-ecom-btn zy-ecom-btn--ghost zy-ecom-btn--block">Continue shopping</a>
        </div>
    </div>
</div>
