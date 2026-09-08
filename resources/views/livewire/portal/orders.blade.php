<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Commerce"
        title="Orders"
        lead="Direct-buy orders from the catalogue and sales orders from accepted quotations."
        icon="folder"
    />

    <x-portal.list-toolbar
        search-model="search"
        placeholder="Search orders…"
        export-action="export"
    />

    <div class="zy-portal-stack">
        @forelse ($orders as $row)
            @php($order = $row['model'])
            <article class="zy-portal-panel zy-portal-panel--lift">
                <div class="zy-portal-quote-row">
                    <div class="zy-portal-panel__title-wrap" style="align-items: start;">
                        <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="folder" /></span>
                        <div>
                            @if ($row['kind'] === 'sales')
                                <p class="zy-eyebrow">{{ $order->reference_number }}</p>
                                <h2 class="zy-portal-panel__title">
                                    @if ($order->quotation)
                                        From quote {{ $order->quotation->reference_number }}
                                    @else
                                        Sales order
                                    @endif
                                </h2>
                            @else
                                <p class="zy-eyebrow">{{ $order->order_number }}</p>
                                <h2 class="zy-portal-panel__title">Direct catalogue order</h2>
                            @endif
                            <p class="zy-muted">
                                {{ number_format((float) $order->total_amount, 2) }} {{ $order->currency }}
                                · {{ $order->items->count() }} line{{ $order->items->count() === 1 ? '' : 's' }}
                            </p>
                        </div>
                    </div>
                    <div class="zy-portal-actions">
                        <span class="zy-badge zy-badge--primary">{{ $order->status->label() }}</span>
                        @if ($row['kind'] === 'sales' && $order->invoice)
                            <a href="{{ route('portal.invoices') }}" class="zy-btn zy-btn--ghost zy-btn--sm">
                                Invoice {{ $order->invoice->reference_number }}
                            </a>
                        @endif
                        @if ($row['kind'] === 'direct')
                            <span class="zy-badge">{{ $order->payment_status->label() }}</span>
                            @if ($order->fulfillment)
                                <span class="zy-badge">{{ $order->fulfillment->status->label() }}@if ($order->fulfillment->tracking_number) · {{ $order->fulfillment->tracking_number }}@endif</span>
                            @endif
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <x-ui.empty-state
                class="zy-portal-panel"
                title="No orders yet"
                description="Direct-buy orders and accepted-quote sales orders appear here."
                :lottie="asset('media/lottie/no-connection.lottie')"
            >
                <x-slot:actions>
                    <a href="{{ route('products.index') }}" class="zy-btn zy-btn--primary">Browse products</a>
                    <a href="{{ route('portal.quotations') }}" class="zy-btn zy-btn--ghost">View quotations</a>
                </x-slot:actions>
            </x-ui.empty-state>
        @endforelse
    </div>
</div>
