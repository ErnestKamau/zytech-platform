<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Sales"
        title="Quotations"
        lead="Review sent quotations, request revisions, accept when ready, and optionally upload a purchase order."
        icon="document"
    >
        <a href="{{ route('quote.index') }}" class="zy-btn zy-btn--primary zy-btn--sm">
            <x-portal.icon name="plus" />
            Request a quote
        </a>
    </x-portal.page-header>

    @if (session('status'))
        <p class="zy-alert zy-alert--success" style="margin-bottom: var(--zy-space-4);">{{ session('status') }}</p>
    @endif

    <x-portal.list-toolbar
        search-model="search"
        filter-model="status"
        :filter-options="$statusOptions"
        filter-label="Status"
        placeholder="Search quotations…"
        export-action="export"
    />

    <div wire:loading.delay class="zy-portal-stack" style="margin-bottom: var(--zy-space-4);">
        <x-ui.skeleton-grid :count="3" variant="line" />
    </div>

    <div class="zy-portal-stack" wire:loading.delay.remove>
        @forelse ($quotations as $quotation)
            @php
                $shared = $quotation->isSharedWithClient();
                $reviewable = $shared && in_array($quotation->status, [
                    \App\Core\Enums\QuotationStatus::Sent,
                    \App\Core\Enums\QuotationStatus::Viewed,
                ], true);
            @endphp
            <a href="{{ route('portal.quotations.show', $quotation) }}" class="zy-portal-panel zy-portal-panel--lift" style="text-decoration:none;color:inherit;">
                <div class="zy-portal-quote-row">
                    <div class="zy-portal-panel__title-wrap" style="align-items: start;">
                        <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="document" /></span>
                        <div>
                            <p class="zy-eyebrow">{{ $quotation->reference_number }} · v{{ $quotation->revision_number }}</p>
                            <h2 class="zy-portal-panel__title">{{ $quotation->title }}</h2>
                            @if ($shared)
                                <p class="zy-muted">
                                    @if ($quotation->valid_until)
                                        Valid until {{ $quotation->valid_until->toFormattedDateString() }}
                                    @else
                                        Validity to be confirmed
                                    @endif
                                    @if ($quotation->total_amount)
                                        · {{ number_format((float) $quotation->total_amount, 2) }} {{ $quotation->currency }}
                                    @endif
                                </p>
                            @else
                                <p class="zy-muted">Our team is pricing this quotation. We'll notify you as soon as it's ready to review and accept.</p>
                            @endif
                            @if ($quotation->salesOrder)
                                <p class="zy-muted" style="margin-top: var(--zy-space-2);">
                                    Order {{ $quotation->salesOrder->reference_number }}
                                    @if ($quotation->salesOrder->invoice)
                                        · Invoice {{ $quotation->salesOrder->invoice->reference_number }} ({{ $quotation->salesOrder->invoice->status->label() }})
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="zy-portal-actions">
                        <span class="zy-badge zy-badge--primary">{{ $shared ? $quotation->status->label() : 'Being prepared' }}</span>
                        @if ($reviewable)
                            <span class="zy-btn zy-btn--primary zy-btn--sm">Review quotation</span>
                        @elseif ($shared)
                            <span class="zy-btn zy-btn--ghost zy-btn--sm">View details</span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <x-ui.empty-state
                class="zy-portal-panel"
                title="No quotations linked"
                description="No quotations are linked to your account. Start with a new request and we will follow up."
                :lottie="asset('media/lottie/no-connection.lottie')"
            >
                <x-slot:actions>
                    <a href="{{ route('quote.index') }}" class="zy-btn zy-btn--primary">Request a quote</a>
                </x-slot:actions>
            </x-ui.empty-state>
        @endforelse
    </div>
</div>
