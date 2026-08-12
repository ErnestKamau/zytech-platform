<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <div class="zy-portal-page__intro">
            <p class="zy-section__eyebrow">Sales</p>
            <h1 class="zy-portal-page__title">Quotations</h1>
            <p class="zy-portal-page__lead">Review sent quotations, track status, and accept when you are ready to proceed.</p>
        </div>
        <div class="zy-portal-page__actions">
            <a href="{{ route('quote.index') }}" class="zy-btn zy-btn--primary zy-btn--sm">Request a quote</a>
        </div>
    </header>

    <div class="zy-portal-stack">
        @forelse ($quotations as $quotation)
            <article class="zy-portal-panel">
                <div class="zy-portal-row">
                    <div>
                        <p class="zy-eyebrow">{{ $quotation->reference_number }}</p>
                        <h2 class="zy-portal-panel__title">{{ $quotation->title }}</h2>
                        <p class="zy-muted">
                            @if ($quotation->valid_until)
                                Valid until {{ $quotation->valid_until->toFormattedDateString() }}
                            @else
                                Validity to be confirmed
                            @endif
                        </p>
                    </div>
                    <div class="zy-portal-actions">
                        <span class="zy-badge zy-badge--primary">{{ $quotation->status->label() }}</span>
                        @if ($quotation->status === \App\Core\Enums\QuotationStatus::Sent)
                            <button type="button" class="zy-btn zy-btn--primary zy-btn--sm" wire:click="accept('{{ $quotation->id }}')">Accept</button>
                            <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="reject('{{ $quotation->id }}')" wire:confirm="Reject this quotation?">Reject</button>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="zy-portal-empty">
                <p class="zy-section__eyebrow">Nothing here yet</p>
                <p>No quotations are linked to your account. Start with a new request and we will follow up.</p>
                <a href="{{ route('quote.index') }}" class="zy-btn zy-btn--primary zy-btn--sm">Request a quote</a>
            </div>
        @endforelse
    </div>
</div>
