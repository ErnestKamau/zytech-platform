<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Commerce"
        title="Invoices"
        lead="Draft and issued invoices linked to your sales orders."
        icon="document"
    />

    <x-portal.list-toolbar
        search-model="search"
        placeholder="Search invoices…"
        export-action="export"
    />

    <div class="zy-portal-stack">
        @forelse ($invoices as $invoice)
            <article class="zy-portal-panel zy-portal-panel--lift">
                <div class="zy-portal-quote-row">
                    <div class="zy-portal-panel__title-wrap" style="align-items: start;">
                        <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="document" /></span>
                        <div>
                            <p class="zy-eyebrow">{{ $invoice->reference_number }}</p>
                            <h2 class="zy-portal-panel__title">
                                @if ($invoice->salesOrder)
                                    Order {{ $invoice->salesOrder->reference_number }}
                                @else
                                    Invoice
                                @endif
                            </h2>
                            <p class="zy-muted">
                                Total {{ number_format((float) $invoice->total_amount, 2) }} {{ $invoice->currency }}
                                · Due {{ number_format((float) $invoice->amount_due, 2) }} {{ $invoice->currency }}
                            </p>
                        </div>
                    </div>
                    <div class="zy-portal-actions">
                        <span class="zy-badge zy-badge--primary">{{ $invoice->status->label() }}</span>
                    </div>
                </div>
            </article>
        @empty
            <x-ui.empty-state
                class="zy-portal-panel"
                title="No invoices yet"
                description="Draft invoices are created when a quotation is accepted."
                :lottie="asset('media/lottie/no-connection.lottie')"
            >
                <x-slot:actions>
                    <a href="{{ route('portal.orders') }}" class="zy-btn zy-btn--primary">View orders</a>
                </x-slot:actions>
            </x-ui.empty-state>
        @endforelse
    </div>
</div>
