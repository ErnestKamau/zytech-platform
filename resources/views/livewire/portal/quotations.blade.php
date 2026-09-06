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
                $reviewable = in_array($quotation->status, [
                    \App\Core\Enums\QuotationStatus::Sent,
                    \App\Core\Enums\QuotationStatus::Viewed,
                ], true);
            @endphp
            <article class="zy-portal-panel zy-portal-panel--lift">
                <div class="zy-portal-quote-row">
                    <div class="zy-portal-panel__title-wrap" style="align-items: start;">
                        <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="document" /></span>
                        <div>
                            <p class="zy-eyebrow">{{ $quotation->reference_number }} · v{{ $quotation->revision_number }}</p>
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
                            @if ($quotation->status === \App\Core\Enums\QuotationStatus::RevisionRequested && $quotation->revision_notes)
                                <p class="zy-muted" style="margin-top: var(--zy-space-2);">Revision notes: {{ $quotation->revision_notes }}</p>
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
                        <span class="zy-badge zy-badge--primary">{{ $quotation->status->label() }}</span>
                        <a href="{{ route('portal.quotations.pdf', $quotation) }}" target="_blank" class="zy-btn zy-btn--ghost zy-btn--sm">
                            <x-portal.icon name="eye" />
                            View PDF
                        </a>
                        <a href="{{ route('portal.quotations.pdf.download', $quotation) }}" class="zy-btn zy-btn--secondary zy-btn--sm">
                            <x-portal.icon name="download" />
                            Download
                        </a>
                        @if ($reviewable)
                            <button type="button" class="zy-btn zy-btn--primary zy-btn--sm" wire:click="accept('{{ $quotation->id }}')">Accept</button>
                            <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="reject('{{ $quotation->id }}')" wire:confirm="Reject this quotation?">Reject</button>
                        @endif
                        @if ($quotation->status === \App\Core\Enums\QuotationStatus::Accepted && ! $quotation->purchaseOrder)
                            <button type="button" class="zy-btn zy-btn--secondary zy-btn--sm" wire:click="startPoUpload('{{ $quotation->id }}')">Upload PO</button>
                        @endif
                    </div>
                </div>

                @if ($reviewable)
                    <div style="margin-top: var(--zy-space-4); display: grid; gap: var(--zy-space-2);">
                        <label class="zy-label" for="revision-{{ $quotation->id }}">Request a revision</label>
                        <textarea id="revision-{{ $quotation->id }}" class="zy-textarea" rows="2" wire:model="revisionNotes" placeholder="Tell us what should change…"></textarea>
                        @error('revisionNotes') <p class="zy-form-error">{{ $message }}</p> @enderror
                        <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" style="justify-self: start;" wire:click="requestRevision('{{ $quotation->id }}')">
                            Request revision
                        </button>
                    </div>
                @endif

                @if ($poQuotationId === $quotation->id)
                    <form wire:submit="uploadPo" style="margin-top: var(--zy-space-4); display: grid; gap: var(--zy-space-3);">
                        <h3 class="zy-portal-panel__title">Upload purchase order</h3>
                        <div>
                            <label class="zy-label" for="po-number">PO number</label>
                            <input id="po-number" type="text" class="zy-input" wire:model="poNumber" required>
                            @error('poNumber') <p class="zy-form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="zy-label" for="po-file">PO document</label>
                            <input id="po-file" type="file" class="zy-input" wire:model="poFile" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                            @error('poFile') <p class="zy-form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="zy-label" for="po-notes">Notes (optional)</label>
                            <textarea id="po-notes" class="zy-textarea" rows="2" wire:model="poNotes"></textarea>
                        </div>
                        <div class="zy-portal-actions">
                            <button type="submit" class="zy-btn zy-btn--primary zy-btn--sm">Submit PO</button>
                            <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="$set('poQuotationId', null)">Cancel</button>
                        </div>
                    </form>
                @endif
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
