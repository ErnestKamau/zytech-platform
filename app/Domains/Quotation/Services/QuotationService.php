<?php

namespace App\Domains\Quotation\Services;

use App\Core\Enums\ApprovalStatus;
use App\Core\Enums\ClientTimelineEvent;
use App\Core\Enums\QuotationStatus;
use App\Core\Enums\QuotationType;
use App\Core\Enums\RevisionStatus;
use App\Core\Services\BaseService;
use App\Domains\Client\Services\TimelineService;
use App\Domains\Commerce\Actions\CreateSalesOrderAndDraftInvoice;
use App\Domains\Commerce\Actions\GenerateProformaInvoiceFromQuotation;
use App\Domains\Operations\Services\ActivityLogger;
use App\Domains\Quotation\Events\QuotationAccepted;
use App\Domains\Quotation\Events\QuotationApproved;
use App\Domains\Quotation\Events\QuotationCreated;
use App\Domains\Quotation\Events\QuotationRejected;
use App\Domains\Quotation\Events\QuotationRevisionRequested;
use App\Domains\Quotation\Events\QuotationSent;
use App\Domains\Quotation\Support\ReferenceNumber;
use App\Models\Quotation;
use App\Models\QuotationApproval;
use App\Models\QuotationItem;
use App\Models\QuotationRequest;
use App\Models\QuotationRevision;
use App\Models\QuotationStatusHistory;
use DomainException;
use Illuminate\Support\Facades\DB;

final class QuotationService extends BaseService
{
    public const DEFAULT_TERMS = <<<'TERMS'
        1. This quotation is valid for 30 days from the date of issue.
        2. A 50% deposit is required before work begins; the balance is due on completion.
        3. Prices exclude statutory approvals and permits unless stated otherwise.
        4. Any work outside the quoted scope will be quoted and approved separately.
        5. Materials remain the property of Zytech Contractors until paid in full.
        TERMS;

    public function __construct(
        private readonly PricingService $pricing,
        private readonly ActivityLogger $activities,
        private readonly TimelineService $timeline,
    ) {}

    private function recordTimeline(Quotation $quotation, ClientTimelineEvent $event, string $title, ?string $description = null): void
    {
        if ($quotation->client === null) {
            return;
        }

        $this->timeline->record($quotation->client, $event, $title, $description, [
            'quotation_id' => $quotation->id,
        ]);
    }

    public function createFromRequest(QuotationRequest $request, ?string $title = null): Quotation
    {
        $quotation = DB::transaction(function () use ($request, $title): Quotation {
            $request->loadMissing(['items.product', 'items.variant', 'products.defaultUnit', 'services']);

            $quotation = Quotation::query()->create([
                'reference_number' => ReferenceNumber::forQuotation(),
                'quotation_request_id' => $request->id,
                'sales_lead_id' => $request->sales_lead_id,
                'client_id' => $request->client_id,
                'title' => $title ?? 'Quotation for '.$request->full_name,
                'type' => QuotationType::Standard,
                'status' => QuotationStatus::Draft,
                'valid_until' => now()->addDays(30)->toDateString(),
                'tax_rate' => PricingService::DEFAULT_TAX_RATE,
                'terms' => self::DEFAULT_TERMS,
                'prepared_by' => auth()->id(),
            ]);

            $this->seedItemsFromRequest($quotation, $request);
            $this->recalculate($quotation);

            $this->recordStatus($quotation, null, QuotationStatus::Draft, 'Created from request '.$request->reference_number);

            QuotationApproval::query()->create([
                'quotation_id' => $quotation->id,
                'status' => ApprovalStatus::Pending,
            ]);

            app(QuotationRequestService::class)->transition(
                $request,
                QuotationStatus::Preparing,
                'Quotation '.$quotation->reference_number.' drafted',
            );

            return $quotation->fresh(['request', 'items']) ?? $quotation->refresh();
        });

        event(new QuotationCreated($quotation));

        return $quotation->refresh();
    }

    private function seedItemsFromRequest(Quotation $quotation, QuotationRequest $request): void
    {
        $sort = 0;

        if ($request->items->isNotEmpty()) {
            foreach ($request->items as $line) {
                $sort++;
                $unitPrice = (float) ($line->variant?->price_amount
                    ?? $line->product?->price_amount
                    ?? 0);

                if ($unitPrice <= 0 && $line->product_id === null) {
                    $matchedService = $request->services->first(
                        fn ($service): bool => $service->title === $line->description
                    );
                    $unitPrice = (float) ($matchedService?->price_amount ?? 0);
                }

                $quantity = (float) $line->quantity;
                $label = $line->product?->title ?? $line->description;

                QuotationItem::query()->create([
                    'quotation_id' => $quotation->id,
                    'label' => $label,
                    'description' => $line->description,
                    'quantity' => $quantity,
                    'unit' => $line->unit_snapshot,
                    'unit_price' => $unitPrice,
                    'line_total' => $this->pricing->lineTotal($quantity, $unitPrice),
                    'is_optional' => false,
                    'sort_order' => $sort,
                ]);
            }

            return;
        }

        foreach ($request->products as $product) {
            $sort++;
            $unitPrice = (float) ($product->price_amount ?? 0);
            QuotationItem::query()->create([
                'quotation_id' => $quotation->id,
                'label' => $product->title,
                'description' => $product->title,
                'quantity' => 1,
                'unit' => $product->defaultUnit?->symbol ?? $product->unit_of_measure,
                'unit_price' => $unitPrice,
                'line_total' => $this->pricing->lineTotal(1, $unitPrice),
                'is_optional' => false,
                'sort_order' => $sort,
            ]);
        }

        foreach ($request->services as $service) {
            $sort++;
            $unitPrice = (float) ($service->price_amount ?? 0);
            QuotationItem::query()->create([
                'quotation_id' => $quotation->id,
                'label' => $service->title,
                'description' => $service->title,
                'quantity' => 1,
                'unit' => 'service',
                'unit_price' => $unitPrice,
                'line_total' => $this->pricing->lineTotal(1, $unitPrice),
                'is_optional' => false,
                'sort_order' => $sort,
            ]);
        }
    }

    public function recalculate(Quotation $quotation): Quotation
    {
        $quotation->load('items');

        foreach ($quotation->items as $item) {
            $lineTotal = $this->pricing->lineTotal((float) $item->quantity, (float) $item->unit_price);

            if ((float) $item->line_total !== $lineTotal) {
                $item->forceFill(['line_total' => $lineTotal])->save();
            }
        }

        $totals = $this->pricing->totals($quotation);

        $quotation->forceFill($totals)->save();

        return $quotation->refresh();
    }

    public function approve(Quotation $quotation, ?string $notes = null): Quotation
    {
        $quotation = DB::transaction(function () use ($quotation, $notes): Quotation {
            $from = $quotation->status;
            $quotation->forceFill([
                'status' => QuotationStatus::Preparing,
                'approved_by' => auth()->id(),
            ])->save();

            $approval = $quotation->approvals()->latest()->first();
            $approval?->forceFill([
                'status' => ApprovalStatus::Approved,
                'notes' => $notes,
                'reviewer_id' => auth()->id(),
                'reviewed_at' => now(),
            ])->save();

            $this->recordStatus($quotation, $from, QuotationStatus::Preparing, $notes);

            return $quotation->refresh();
        });

        event(new QuotationApproved($quotation));

        return $quotation;
    }

    public function send(Quotation $quotation): Quotation
    {
        $quotation = DB::transaction(function () use ($quotation): Quotation {
            $from = $quotation->status;
            $wasRevision = $from === QuotationStatus::RevisionRequested
                || ($from === QuotationStatus::Preparing && $quotation->revision_requested_at !== null);

            if ($wasRevision || $quotation->revisions()->exists()) {
                $this->publishVersion($quotation, $wasRevision
                    ? 'Revised quotation resent to client'
                    : 'Issued quotation version');
            } else {
                $this->publishVersion($quotation, 'Initial quotation issued');
            }

            $quotation->forceFill([
                'status' => QuotationStatus::Sent,
                'sent_at' => now(),
                'revision_notes' => null,
            ])->save();

            $this->recordStatus($quotation, $from, QuotationStatus::Sent, 'Sent to client');

            $this->activities->log($quotation, 'quotation.sent', [
                'reference' => $quotation->reference_number,
                'from_status' => $from->value,
            ]);

            $this->recordTimeline(
                $quotation,
                ClientTimelineEvent::QuotationSent,
                'Quotation '.$quotation->reference_number.' sent',
                number_format((float) $quotation->total_amount, 2).' '.$quotation->currency,
            );

            return $quotation->refresh();
        });

        event(new QuotationSent($quotation));

        return $quotation;
    }

    public const SENDABLE_STATUSES = [
        QuotationStatus::Draft,
        QuotationStatus::Pending,
        QuotationStatus::Reviewing,
        QuotationStatus::Preparing,
        QuotationStatus::RevisionRequested,
    ];

    /**
     * Approves an unapproved draft, then issues it to the client portal.
     */
    public function sendToClient(Quotation $quotation): Quotation
    {
        if (! in_array($quotation->status, self::SENDABLE_STATUSES, true)) {
            throw new DomainException('Only draft or in-progress quotations can be sent.');
        }

        $quotation = $this->recalculate($quotation);

        if ($quotation->items->where('is_optional', false)->isEmpty() || (float) $quotation->total_amount <= 0) {
            throw new DomainException('Add at least one priced line item before sending.');
        }

        if (in_array($quotation->status, [QuotationStatus::Draft, QuotationStatus::Pending, QuotationStatus::Reviewing], true)) {
            $quotation = $this->approve($quotation, 'Approved on send');
        }

        return $this->send($quotation);
    }

    public function requestRevision(Quotation $quotation, string $notes): Quotation
    {
        $quotation = DB::transaction(function () use ($quotation, $notes): Quotation {
            $from = $quotation->status;
            $quotation->forceFill([
                'status' => QuotationStatus::RevisionRequested,
                'revision_notes' => $notes,
                'revision_requested_at' => now(),
            ])->save();

            $this->recordStatus($quotation, $from, QuotationStatus::RevisionRequested, $notes);

            $this->recordTimeline(
                $quotation,
                ClientTimelineEvent::QuotationRevisionRequested,
                'Revision requested for '.$quotation->reference_number,
                $notes,
            );

            return $quotation->refresh();
        });

        event(new QuotationRevisionRequested($quotation));

        return $quotation;
    }

    public function accept(Quotation $quotation): Quotation
    {
        $accepted = DB::transaction(function () use ($quotation): Quotation {
            $from = $quotation->status;
            $quotation->forceFill([
                'status' => QuotationStatus::Accepted,
                'accepted_at' => now(),
            ])->save();

            $this->recordStatus($quotation, $from, QuotationStatus::Accepted, 'Accepted by client');

            if ($request = $quotation->request) {
                app(QuotationRequestService::class)->transition($request, QuotationStatus::Accepted, 'Linked quotation accepted');
            }

            $accepted = $quotation->refresh();

            $this->activities->log($accepted, 'quotation.accepted', [
                'reference' => $accepted->reference_number,
                'from_status' => $from->value,
            ]);

            $this->recordTimeline(
                $accepted,
                ClientTimelineEvent::QuotationAccepted,
                'Quotation '.$accepted->reference_number.' accepted',
                number_format((float) $accepted->total_amount, 2).' '.$accepted->currency,
            );

            app(CreateSalesOrderAndDraftInvoice::class)->handle($accepted);
            app(GenerateProformaInvoiceFromQuotation::class)->handle($accepted);

            return $accepted->refresh();
        });

        event(new QuotationAccepted($accepted));

        return $accepted;
    }

    public function reject(Quotation $quotation, ?string $notes = null): Quotation
    {
        $quotation = DB::transaction(function () use ($quotation, $notes): Quotation {
            $from = $quotation->status;
            $quotation->forceFill([
                'status' => QuotationStatus::Rejected,
                'rejected_at' => now(),
            ])->save();

            $this->recordStatus($quotation, $from, QuotationStatus::Rejected, $notes);

            $this->activities->log($quotation, 'quotation.rejected', [
                'reference' => $quotation->reference_number,
                'from_status' => $from->value,
                'notes' => $notes,
            ]);

            $this->recordTimeline(
                $quotation,
                ClientTimelineEvent::QuotationRejected,
                'Quotation '.$quotation->reference_number.' rejected',
                $notes,
            );

            return $quotation->refresh();
        });

        event(new QuotationRejected($quotation));

        return $quotation;
    }

    public function syncItemTotals(QuotationItem $item): QuotationItem
    {
        $lineTotal = $this->pricing->lineTotal((float) $item->quantity, (float) $item->unit_price);
        $item->forceFill(['line_total' => $lineTotal])->save();

        if ($item->quotation) {
            $this->recalculate($item->quotation);
        }

        return $item->refresh();
    }

    private function publishVersion(Quotation $quotation, string $summary): void
    {
        QuotationRevision::query()
            ->where('quotation_id', $quotation->id)
            ->where('status', RevisionStatus::Published)
            ->update(['status' => RevisionStatus::Superseded]);

        $existingCount = $quotation->revisions()->count();
        $next = $existingCount === 0
            ? max(1, (int) $quotation->revision_number)
            : ((int) $quotation->revision_number) + 1;

        if ($existingCount === 0 && $quotation->revision_requested_at !== null) {
            $next = max(2, ((int) $quotation->revision_number) + 1);
        }

        $quotation->forceFill(['revision_number' => $next])->save();

        QuotationRevision::query()->create([
            'quotation_id' => $quotation->id,
            'revision_number' => $next,
            'status' => RevisionStatus::Published,
            'summary' => $summary,
            'created_by' => auth()->id(),
        ]);
    }

    private function recordStatus(
        Quotation $quotation,
        ?QuotationStatus $from,
        QuotationStatus $to,
        ?string $notes = null,
    ): void {
        QuotationStatusHistory::query()->create([
            'quotation_id' => $quotation->id,
            'quotation_request_id' => $quotation->quotation_request_id,
            'from_status' => $from?->value,
            'to_status' => $to->value,
            'notes' => $notes,
            'changed_by' => auth()->id(),
        ]);
    }
}
