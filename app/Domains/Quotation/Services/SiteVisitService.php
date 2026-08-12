<?php

namespace App\Domains\Quotation\Services;

use App\Core\Enums\SiteVisitStatus;
use App\Core\Services\BaseService;
use App\Domains\Quotation\Events\SiteVisitScheduled;
use App\Models\SiteVisit;

final class SiteVisitService extends BaseService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function schedule(array $attributes): SiteVisit
    {
        $visit = SiteVisit::query()->create([
            ...$attributes,
            'status' => SiteVisitStatus::Scheduled,
            'scheduled_by' => auth()->id(),
        ]);

        event(new SiteVisitScheduled($visit->fresh(['quotationRequest', 'salesLead'])));

        return $visit->refresh();
    }
}
