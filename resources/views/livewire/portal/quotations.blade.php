<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <h1 class="zy-portal-page__title">Quotations</h1>
        <a href="{{ route('quote.index') }}" class="zy-btn zy-btn--primary zy-btn--sm">Request a quote</a>
    </header>

    <div class="zy-portal-stack">
        @forelse ($quotations as $quotation)
            <article class="zy-portal-panel">
                <div class="zy-portal-row">
                    <div>
                        <strong>{{ $quotation->reference_number }}</strong>
                        <p class="zy-muted">{{ $quotation->title }}</p>
                        <p class="zy-muted">
                            Status: {{ $quotation->status->label() }}
                            @if ($quotation->valid_until)
                                · Valid until {{ $quotation->valid_until->toFormattedDateString() }}
                            @endif
                        </p>
                    </div>
                    <div class="zy-portal-actions">
                        @if ($quotation->status === \App\Core\Enums\QuotationStatus::Sent)
                            <button type="button" class="zy-btn zy-btn--primary zy-btn--sm" wire:click="accept('{{ $quotation->id }}')">Accept</button>
                            <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="reject('{{ $quotation->id }}')" wire:confirm="Reject this quotation?">Reject</button>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <p class="zy-muted">No quotations linked to your account yet.</p>
        @endforelse
    </div>
</div>
