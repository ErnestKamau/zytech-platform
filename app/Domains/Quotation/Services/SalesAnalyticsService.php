<?php

namespace App\Domains\Quotation\Services;

use App\Core\Services\BaseService;
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
        $accepted = Quotation::query()->where('status', 'accepted')->count();
        $averageValue = (float) Quotation::query()->where('status', 'accepted')->avg('total_amount');

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
     * @return Collection<int, QuotationRequest>
     */
    public function recentRequests(int $limit = 5): Collection
    {
        return QuotationRequest::query()
            ->orderByDesc('submitted_at')
            ->limit($limit)
            ->get();
    }
}
