<?php

namespace App\Domains\Quotation\Services;

use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\QuotationStatus;
use App\Core\Services\BaseService;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\QuotationRequest;
use App\Models\SalesLead;
use Illuminate\Support\Collection;

final class SalesAnalyticsService extends BaseService
{
    /**
     * Open quote statuses that contribute to sales pipeline value.
     * One stage only — never sum RFQ + quote + invoice for the same deal.
     *
     * @return list<QuotationStatus>
     */
    public static function openPipelineStatuses(): array
    {
        return [
            QuotationStatus::Draft,
            QuotationStatus::Pending,
            QuotationStatus::Reviewing,
            QuotationStatus::Preparing,
            QuotationStatus::Sent,
            QuotationStatus::Viewed,
            QuotationStatus::RevisionRequested,
        ];
    }

    /**
     * @return array<string, int|float>
     */
    public function snapshot(): array
    {
        $requests = QuotationRequest::query()->count();
        $pending = QuotationRequest::query()->where('status', QuotationStatus::Pending)->count();
        $leads = SalesLead::query()->where('status', 'new')->count();
        $pipelineCount = Quotation::query()
            ->whereIn('status', self::openPipelineStatuses())
            ->count();
        $accepted = Quotation::query()->where('status', QuotationStatus::Accepted)->count();
        $averageValue = (float) Quotation::query()->where('status', QuotationStatus::Accepted)->avg('total_amount');

        return [
            'requests_total' => $requests,
            'requests_pending' => $pending,
            'leads_new' => $leads,
            'quotations_pipeline' => $pipelineCount,
            'pipeline_value' => $this->pipelineValue(),
            'quotations_accepted' => $accepted,
            'average_accepted_value' => round($averageValue, 2),
        ];
    }

    /**
     * Monetary value of open quotations only (anti double-count).
     */
    public function pipelineValue(): float
    {
        return round((float) Quotation::query()
            ->whereIn('status', self::openPipelineStatuses())
            ->sum('total_amount'), 2);
    }

    /**
     * RFQ → quote sent → accepted → invoice → paid.
     * Stage totals are current inventory (count + value).
     * Conversions are relationship-based within the last 30 days cohort.
     *
     * @return array{
     *     stages: list<array{key: string, label: string, count: int, value: float|null}>,
     *     conversions: array<string, float>,
     *     period_days: int
     * }
     */
    public function funnel(int $periodDays = 30): array
    {
        $rfqs = QuotationRequest::query()->count();

        $quotesSentQuery = Quotation::query()->whereNotNull('sent_at');
        $quotesSent = (clone $quotesSentQuery)->count();
        $quotesSentValue = (float) (clone $quotesSentQuery)->sum('total_amount');

        $acceptedQuery = Quotation::query()->where('status', QuotationStatus::Accepted);
        $accepted = (clone $acceptedQuery)->count();
        $acceptedValue = (float) (clone $acceptedQuery)->sum('total_amount');

        $invoicesQuery = Invoice::query()->whereNotIn('status', [
            InvoiceStatus::Draft,
            InvoiceStatus::Void,
            InvoiceStatus::Cancelled,
        ]);
        $invoices = (clone $invoicesQuery)->count();
        $invoicesValue = (float) (clone $invoicesQuery)->sum('total_amount');

        $paidQuery = Invoice::query()->where('status', InvoiceStatus::Paid);
        $paid = (clone $paidQuery)->count();
        $paidValue = (float) (clone $paidQuery)->sum('total_amount');

        return [
            'stages' => [
                ['key' => 'rfqs', 'label' => 'RFQs', 'count' => $rfqs, 'value' => null],
                ['key' => 'quotes_sent', 'label' => 'Quotes sent', 'count' => $quotesSent, 'value' => round($quotesSentValue, 2)],
                ['key' => 'accepted', 'label' => 'Accepted', 'count' => $accepted, 'value' => round($acceptedValue, 2)],
                ['key' => 'invoices', 'label' => 'Invoices', 'count' => $invoices, 'value' => round($invoicesValue, 2)],
                ['key' => 'paid', 'label' => 'Paid', 'count' => $paid, 'value' => round($paidValue, 2)],
            ],
            'conversions' => $this->cohortConversions($periodDays),
            'period_days' => $periodDays,
        ];
    }

    /**
     * Relationship-based conversion rates for a rolling cohort window.
     *
     * @return array{
     *     rfqs_to_quotes: float,
     *     quotes_to_accepted: float,
     *     accepted_to_invoices: float,
     *     invoices_to_paid: float
     * }
     */
    public function cohortConversions(int $periodDays = 30): array
    {
        $since = now()->subDays($periodDays);

        $rfqsInCohort = QuotationRequest::query()
            ->where('submitted_at', '>=', $since)
            ->count();
        $rfqsWithQuote = QuotationRequest::query()
            ->where('submitted_at', '>=', $since)
            ->whereHas('quotation')
            ->count();

        $quotesSentInCohort = Quotation::query()
            ->where('sent_at', '>=', $since)
            ->count();
        $quotesAcceptedInCohort = Quotation::query()
            ->where('sent_at', '>=', $since)
            ->where('status', QuotationStatus::Accepted)
            ->count();

        $acceptedInCohort = Quotation::query()
            ->where('accepted_at', '>=', $since)
            ->where('status', QuotationStatus::Accepted)
            ->count();
        $acceptedWithInvoice = Quotation::query()
            ->where('accepted_at', '>=', $since)
            ->where('status', QuotationStatus::Accepted)
            ->where(function ($q): void {
                $q->whereHas('salesOrder.invoice')
                    ->orWhereHas('invoices');
            })
            ->count();

        $invoicesInCohort = Invoice::query()
            ->where('issued_at', '>=', $since)
            ->whereNotIn('status', [
                InvoiceStatus::Draft,
                InvoiceStatus::Void,
                InvoiceStatus::Cancelled,
            ])
            ->count();
        $paidInCohort = Invoice::query()
            ->where('issued_at', '>=', $since)
            ->where('status', InvoiceStatus::Paid)
            ->count();

        return [
            'rfqs_to_quotes' => $this->conversionPercent($rfqsWithQuote, $rfqsInCohort),
            'quotes_to_accepted' => $this->conversionPercent($quotesAcceptedInCohort, $quotesSentInCohort),
            'accepted_to_invoices' => $this->conversionPercent($acceptedWithInvoice, $acceptedInCohort),
            'invoices_to_paid' => $this->conversionPercent($paidInCohort, $invoicesInCohort),
        ];
    }

    /**
     * @return Collection<int, QuotationRequest>
     */
    public function recentRequests(int $limit = 5): Collection
    {
        return QuotationRequest::query()
            ->orderByDesc('submitted_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, QuotationRequest>
     */
    public function pendingRequests(int $limit = 8): Collection
    {
        return QuotationRequest::query()
            ->where('status', QuotationStatus::Pending)
            ->orderByDesc('submitted_at')
            ->limit($limit)
            ->get();
    }

    private function conversionPercent(int $numerator, int $denominator): float
    {
        if ($denominator === 0) {
            return 0.0;
        }

        return round(($numerator / $denominator) * 100, 1);
    }
}
