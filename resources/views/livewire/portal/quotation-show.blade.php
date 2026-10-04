<div class="zy-portal-page">
    <a href="{{ route('portal.quotations') }}" class="zy-muted" style="text-decoration:none; display:inline-flex; align-items:center; gap:.35rem; margin-bottom: var(--zy-space-2);">
        &larr; Back to quotations
    </a>

    <x-portal.page-header
        eyebrow="Quotation"
        :title="$quotation->title"
        :lead="$quotation->reference_number.' · v'.$quotation->revision_number"
        icon="document"
    >
        <span class="zy-badge zy-badge--primary">{{ $quotation->isSharedWithClient() ? $quotation->status->label() : 'Being prepared' }}</span>
        @if ($quotation->isSharedWithClient())
            <a href="{{ route('portal.quotations.pdf', $quotation) }}" target="_blank" class="zy-btn zy-btn--ghost zy-btn--sm">
                <x-portal.icon name="eye" /> View PDF
            </a>
            <a href="{{ route('portal.quotations.pdf.download', $quotation) }}" class="zy-btn zy-btn--secondary zy-btn--sm">
                <x-portal.icon name="download" /> Download
            </a>
        @endif
    </x-portal.page-header>

    @if (session('status'))
        <p class="zy-alert zy-alert--success">{{ session('status') }}</p>
    @endif

    <div class="zy-portal-split">
        <div class="zy-portal-stack">
            {{-- Line items --}}
            <div class="zy-portal-panel">
                <div class="zy-portal-panel__header">
                    <h2 class="zy-portal-panel__title">Scope &amp; pricing</h2>
                </div>

                @if ($quotation->isSharedWithClient())
                    @forelse ($quotation->sections as $section)
                        @if ($section->items->isNotEmpty())
                            <p class="zy-eyebrow" style="margin-top: var(--zy-space-3);">{{ $section->title }}</p>
                            @foreach ($section->items as $item)
                                <div class="zy-portal-row">
                                    <div>
                                        <p class="zy-portal-row__title">{{ $item->label }}{{ $item->is_optional ? ' (optional)' : '' }}</p>
                                        @if ($item->description)
                                            <p class="zy-portal-row__meta">{{ $item->description }}</p>
                                        @endif
                                    </div>
                                    <p class="zy-portal-row__title">{{ number_format((float) $item->line_total, 2) }} {{ $quotation->currency }}</p>
                                </div>
                            @endforeach
                        @endif
                    @empty
                    @endforelse

                    @php $unsectioned = $quotation->items->whereNull('quotation_section_id'); @endphp
                    @if ($unsectioned->isNotEmpty())
                        @foreach ($unsectioned as $item)
                            <div class="zy-portal-row">
                                <div>
                                    <p class="zy-portal-row__title">{{ $item->label }}{{ $item->is_optional ? ' (optional)' : '' }}</p>
                                    @if ($item->description)
                                        <p class="zy-portal-row__meta">{{ $item->description }}</p>
                                    @endif
                                </div>
                                <p class="zy-portal-row__title">{{ number_format((float) $item->line_total, 2) }} {{ $quotation->currency }}</p>
                            </div>
                        @endforeach
                    @endif

                    <div class="zy-portal-row" style="border-top: 2px solid var(--zy-color-border); margin-top: var(--zy-space-3); padding-top: var(--zy-space-3);">
                        <p class="zy-portal-row__title" style="font-size: var(--zy-text-lg);">Total</p>
                        <p class="zy-portal-row__title" style="font-size: var(--zy-text-lg);">{{ number_format((float) $quotation->total_amount, 2) }} {{ $quotation->currency }}</p>
                    </div>
                @else
                    <p class="zy-muted">Our team is pricing this quotation. We'll notify you as soon as it's ready to review.</p>
                @endif
            </div>

            {{-- Actions --}}
            @if ($reviewable)
                <div class="zy-portal-panel">
                    <h2 class="zy-portal-panel__title">Your decision</h2>
                    <p class="zy-muted">Review the scope above, then accept, reject, or ask us to revise it.</p>
                    <div class="zy-portal-actions">
                        <button type="button" class="zy-btn zy-btn--primary zy-btn--sm" wire:click="openModal('accept')">Accept quotation</button>
                        <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="openModal('revision')">Request revision</button>
                        <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="openModal('reject')">Reject</button>
                    </div>
                </div>
            @endif

            @if ($quotation->status === \App\Core\Enums\QuotationStatus::RevisionRequested && $quotation->revision_notes)
                <div class="zy-portal-panel">
                    <h2 class="zy-portal-panel__title">Revision requested</h2>
                    <p class="zy-muted">{{ $quotation->revision_notes }}</p>
                </div>
            @endif

            {{-- Proforma invoice --}}
            @if ($proforma)
                <div class="zy-portal-panel">
                    <div class="zy-portal-panel__header">
                        <h2 class="zy-portal-panel__title">Proforma invoice</h2>
                        <span class="zy-badge zy-badge--primary">{{ $proforma->status->label() }}</span>
                    </div>
                    <div class="zy-portal-row">
                        <div>
                            <p class="zy-portal-row__title">{{ $proforma->reference_number }}</p>
                            <p class="zy-portal-row__meta">{{ number_format((float) $proforma->total_amount, 2) }} {{ $proforma->currency }}</p>
                        </div>
                        <div class="zy-portal-actions">
                            <a href="{{ route('portal.proforma.pdf', $proforma) }}" target="_blank" class="zy-btn zy-btn--ghost zy-btn--sm"><x-portal.icon name="eye" /> View</a>
                            <a href="{{ route('portal.proforma.pdf.download', $proforma) }}" class="zy-btn zy-btn--secondary zy-btn--sm"><x-portal.icon name="download" /> Download</a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- PO upload --}}
            @if ($quotation->status === \App\Core\Enums\QuotationStatus::Accepted && ! $quotation->purchaseOrder)
                <div class="zy-portal-panel">
                    <div class="zy-portal-panel__header">
                        <h2 class="zy-portal-panel__title">Purchase order</h2>
                        @if (! $showPoForm)
                            <button type="button" class="zy-btn zy-btn--secondary zy-btn--sm" wire:click="$set('showPoForm', true)">Upload PO</button>
                        @endif
                    </div>
                    @if ($showPoForm)
                        <form wire:submit="uploadPo" class="zy-portal-form zy-portal-form--flush">
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
                                <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="$set('showPoForm', false)">Cancel</button>
                            </div>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        {{-- Timeline sidebar --}}
        <div class="zy-portal-panel">
            <h2 class="zy-portal-panel__title">Timeline</h2>
            @if ($timeline->isEmpty())
                <p class="zy-muted">No activity yet.</p>
            @else
                <div class="zy-portal-timeline">
                    @foreach ($timeline as $event)
                        <article class="zy-portal-timeline__item">
                            <span class="zy-portal-timeline__icon" aria-hidden="true"><x-portal.icon name="check" /></span>
                            <p class="zy-eyebrow">{{ $event->event_type->label() }}</p>
                            <h3 class="zy-portal-panel__title" style="font-size: var(--zy-text-sm);">{{ $event->title }}</h3>
                            <p class="zy-muted">{{ $event->occurred_at?->toDayDateTimeString() }}</p>
                            @if ($event->description)
                                <p class="zy-muted">{{ $event->description }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Accept modal (2-step) --}}
    @if ($activeModal === 'accept')
        <div class="zy-modal" wire:keydown.escape="closeModal">
            <div class="zy-modal__backdrop" wire:click="closeModal"></div>
            <div class="zy-modal__panel">
                <div class="zy-modal__header">
                    <h2 class="zy-modal__title">Accept {{ $quotation->reference_number }}</h2>
                    <button type="button" class="zy-modal__close" wire:click="closeModal" aria-label="Close">
                        <x-portal.icon name="x-mark" />
                    </button>
                </div>

                <div class="zy-portal-progress">
                    <span class="zy-portal-progress__node is-done"></span>
                    <span class="zy-portal-progress__line {{ $acceptStep >= 2 ? 'is-done' : '' }}"></span>
                    <span class="zy-portal-progress__node {{ $acceptStep >= 2 ? 'is-done' : '' }}"></span>
                </div>

                @if ($acceptStep === 1)
                    <div class="zy-modal__body">
                        <p class="zy-muted">You're about to accept this quotation for <strong>{{ number_format((float) $quotation->total_amount, 2) }} {{ $quotation->currency }}</strong>. Review the scope on the left before confirming.</p>
                        <p class="zy-muted">Reference: {{ $quotation->reference_number }} · Valid until {{ $quotation->valid_until?->toFormattedDateString() ?? '—' }}</p>
                    </div>
                    <div class="zy-modal__footer">
                        <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="closeModal">Cancel</button>
                        <button type="button" class="zy-btn zy-btn--primary zy-btn--sm" wire:click="nextAcceptStep">Continue</button>
                    </div>
                @else
                    <div class="zy-modal__body">
                        <p class="zy-muted">Accepting will:</p>
                        <ul class="zy-portal-list">
                            <li>Generate a Proforma Invoice you can download immediately</li>
                            <li>Create a Sales Order and a draft Invoice</li>
                            <li>Notify our team to begin fulfilment</li>
                        </ul>
                    </div>
                    <div class="zy-modal__footer">
                        <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="previousAcceptStep">Back</button>
                        <button type="button" class="zy-btn zy-btn--primary zy-btn--sm" wire:click="accept">Confirm acceptance</button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Reject modal --}}
    @if ($activeModal === 'reject')
        <div class="zy-modal" wire:keydown.escape="closeModal">
            <div class="zy-modal__backdrop" wire:click="closeModal"></div>
            <div class="zy-modal__panel">
                <div class="zy-modal__header">
                    <h2 class="zy-modal__title">Reject {{ $quotation->reference_number }}</h2>
                    <button type="button" class="zy-modal__close" wire:click="closeModal" aria-label="Close"><x-portal.icon name="x-mark" /></button>
                </div>
                <div class="zy-modal__body">
                    <label class="zy-label" for="reject-notes">Reason (optional)</label>
                    <textarea id="reject-notes" class="zy-textarea" rows="3" wire:model="reviewNotes" placeholder="Tell us why…"></textarea>
                </div>
                <div class="zy-modal__footer">
                    <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="closeModal">Cancel</button>
                    <button type="button" class="zy-btn zy-btn--primary zy-btn--sm" wire:click="reject">Confirm rejection</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Revision modal --}}
    @if ($activeModal === 'revision')
        <div class="zy-modal" wire:keydown.escape="closeModal">
            <div class="zy-modal__backdrop" wire:click="closeModal"></div>
            <div class="zy-modal__panel">
                <div class="zy-modal__header">
                    <h2 class="zy-modal__title">Request a revision</h2>
                    <button type="button" class="zy-modal__close" wire:click="closeModal" aria-label="Close"><x-portal.icon name="x-mark" /></button>
                </div>
                <div class="zy-modal__body">
                    <label class="zy-label" for="revision-notes">What should change?</label>
                    <textarea id="revision-notes" class="zy-textarea" rows="4" wire:model="reviewNotes" required></textarea>
                    @error('reviewNotes') <p class="zy-form-error">{{ $message }}</p> @enderror
                </div>
                <div class="zy-modal__footer">
                    <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="closeModal">Cancel</button>
                    <button type="button" class="zy-btn zy-btn--primary zy-btn--sm" wire:click="requestRevision">Send revision request</button>
                </div>
            </div>
        </div>
    @endif
</div>
