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
     * @return array<string, int|float>
     */
    public function snapshot(): array
    {
        $requests = QuotationRequest::query()->count();
        $pending = QuotationRequest::query()->where('status', 'pending')->count();
        $leads = SalesLead::query()->where('status', 'new')->count();
        $pipeline = Quotation::query()
            ->whereNotIn('status', ['accepted', 'rejected', 'completed', 'expired'])
            ->count();
        $accepted = Quotation::query()->where('status', QuotationStatus::Accepted)->count();
        $averageValue = (float) Quotation::query()->where('status', QuotationStatus::Accepted)->avg('total_amount');

        return [
            'requests_total' => $requests,
            'requests_pending' => $pending,
            'leads_new' => $leads,
            'quotations_pipeline' => $pipeline,
            'quotations_accepted' => $accepted,
            'average_accepted_value' => round($averageValue, 2),
        ];
    }

    /**
     * RFQ → quote sent → accepted → invoice → paid conversion strip.
     *
     * @return array{
     *     stages: list<array{key: string, label: string, count: int}>,
     *     conversions: array<string, float>
     * }
     */
    public function funnel(): array
    {
        $rfqs = QuotationRequest::query()->count();
        $quotesSent = Quotation::query()->whereNotNull('sent_at')->count();
        $accepted = Quotation::query()->where('status', QuotationStatus::Accepted)->count();
        $invoices = Invoice::query()->count();
        $paid = Invoice::query()->where('status', InvoiceStatus::Paid)->count();

        return [
            'stages' => [
                ['key' => 'rfqs', 'label' => 'RFQs', 'count' => $rfqs],
                ['key' => 'quotes_sent', 'label' => 'Quotes sent', 'count' => $quotesSent],
                ['key' => 'accepted', 'label' => 'Accepted', 'count' => $accepted],
                ['key' => 'invoices', 'label' => 'Invoices', 'count' => $invoices],
                ['key' => 'paid', 'label' => 'Paid', 'count' => $paid],
            ],
            'conversions' => [
                'rfqs_to_quotes' => $this->conversionPercent($quotesSent, $rfqs),
                'quotes_to_accepted' => $this->conversionPercent($accepted, $quotesSent),
                'accepted_to_invoices' => $this->conversionPercent($invoices, $accepted),
                'invoices_to_paid' => $this->conversionPercent($paid, $invoices),
            ],
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
