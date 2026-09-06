<?php

namespace App\Domains\Quotation\Services;

use App\Core\Enums\ApprovalStatus;
use App\Core\Enums\QuotationStatus;
use App\Core\Enums\QuotationType;
use App\Core\Enums\RevisionStatus;
use App\Core\Services\BaseService;
use App\Domains\Commerce\Actions\CreateSalesOrderAndDraftInvoice;
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
use Illuminate\Support\Facades\DB;

final class QuotationService extends BaseService
{
    public function __construct(private readonly PricingService $pricing) {}

    public function createFromRequest(QuotationRequest $request, ?string $title = null): Quotation
    {
        return DB::transaction(function () use ($request, $title): Quotation {
            $quotation = Quotation::query()->create([
                'reference_number' => ReferenceNumber::forQuotation(),
                'quotation_request_id' => $request->id,
                'sales_lead_id' => $request->sales_lead_id,
                'client_id' => $request->client_id,
                'title' => $title ?? 'Quotation for '.$request->full_name,
                'type' => QuotationType::Standard,
                'status' => QuotationStatus::Draft,
                'valid_until' => now()->addDays(30)->toDateString(),
                'terms' => 'Valid for 30 days from issue date. Prices exclude statutory approvals unless stated.',
                'prepared_by' => auth()->id(),
            ]);

            $this->recordStatus($quotation, null, QuotationStatus::Draft, 'Created from request '.$request->reference_number);

            QuotationApproval::query()->create([
                'quotation_id' => $quotation->id,
                'status' => ApprovalStatus::Pending,
            ]);

            event(new QuotationCreated($quotation->fresh(['request'])));

            return $quotation->refresh();
        });
    }

    public function recalculate(Quotation $quotation): Quotation
    {
        $totals = $this->pricing->totals($quotation);

        $quotation->forceFill($totals)->save();

        return $quotation->refresh();
    }

    public function approve(Quotation $quotation, ?string $notes = null): Quotation
    {
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

        event(new QuotationApproved($quotation->refresh()));

        return $quotation;
    }

    public function send(Quotation $quotation): Quotation
    {
        return DB::transaction(function () use ($quotation): Quotation {
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

            event(new QuotationSent($quotation->refresh()));

            return $quotation;
        });
    }

    public function requestRevision(Quotation $quotation, string $notes): Quotation
    {
        $from = $quotation->status;
        $quotation->forceFill([
            'status' => QuotationStatus::RevisionRequested,
            'revision_notes' => $notes,
            'revision_requested_at' => now(),
        ])->save();

        $this->recordStatus($quotation, $from, QuotationStatus::RevisionRequested, $notes);

        event(new QuotationRevisionRequested($quotation->refresh()));

        return $quotation;
    }

    public function accept(Quotation $quotation): Quotation
    {
        return DB::transaction(function () use ($quotation): Quotation {
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
            event(new QuotationAccepted($accepted));

            app(CreateSalesOrderAndDraftInvoice::class)->handle($accepted);

            return $accepted->refresh();
        });
    }

    public function reject(Quotation $quotation, ?string $notes = null): Quotation
    {
        $from = $quotation->status;
        $quotation->forceFill([
            'status' => QuotationStatus::Rejected,
            'rejected_at' => now(),
        ])->save();

        $this->recordStatus($quotation, $from, QuotationStatus::Rejected, $notes);

        event(new QuotationRejected($quotation->refresh()));

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
