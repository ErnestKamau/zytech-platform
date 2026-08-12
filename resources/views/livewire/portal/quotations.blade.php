<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Sales"
        title="Quotations"
        lead="Review sent quotations, track status, and accept when you are ready to proceed."
        icon="document"
    >
        <a href="{{ route('quote.index') }}" class="zy-btn zy-btn--primary zy-btn--sm">
            <x-portal.icon name="plus" />
            Request a quote
        </a>
    </x-portal.page-header>

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
            <article class="zy-portal-panel zy-portal-panel--lift">
                <div class="zy-portal-quote-row">
                    <div class="zy-portal-panel__title-wrap" style="align-items: start;">
                        <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="document" /></span>
                        <div>
                            <p class="zy-eyebrow">{{ $quotation->reference_number }}</p>
                            <h2 class="zy-portal-panel__title">{{ $quotation->title }}</h2>
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
                        </div>
                    </div>
                    <div class="zy-portal-actions">
                        <span class="zy-badge zy-badge--primary">{{ $quotation->status->label() }}</span>
                        <a href="{{ route('portal.quotations.pdf', $quotation) }}" target="_blank" class="zy-btn zy-btn--ghost zy-btn--sm">
                            <x-portal.icon name="eye" />
                            View PDF
                        </a>
                        <a href="{{ route('portal.quotations.pdf.download', $quotation) }}" class="zy-btn zy-btn--secondary zy-btn--sm">
                            <x-portal.icon name="download" />
                            Download
                        </a>
                        <a href="{{ route('portal.quotations.pdf', $quotation) }}" target="_blank" class="zy-btn zy-btn--ghost zy-btn--sm" onclick="setTimeout(() => { try { window.open(this.href).print(); } catch (e) {} }, 400)">
                            <x-portal.icon name="printer" />
                            Print
                        </a>
                        @if ($quotation->status === \App\Core\Enums\QuotationStatus::Sent)
                            <button type="button" class="zy-btn zy-btn--primary zy-btn--sm" wire:click="accept('{{ $quotation->id }}')">Accept</button>
                            <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="reject('{{ $quotation->id }}')" wire:confirm="Reject this quotation?">Reject</button>
                        @endif
                    </div>
                </div>
            </article>
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
