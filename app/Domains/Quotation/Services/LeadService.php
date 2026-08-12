<?php

namespace App\Domains\Quotation\Services;

use App\Core\Enums\LeadStatus;
use App\Core\Enums\PriorityLevel;
use App\Core\Services\BaseService;
use App\Domains\Quotation\Events\LeadCreated;
use App\Domains\Quotation\Events\LeadQualified;
use App\Models\SalesLead;

final class LeadService extends BaseService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): SalesLead
    {
        $lead = SalesLead::query()->create([
            ...$attributes,
            'status' => $attributes['status'] ?? LeadStatus::New,
            'priority' => $attributes['priority'] ?? PriorityLevel::Normal,
        ]);

        event(new LeadCreated($lead));

        return $lead->refresh();
    }

    public function qualify(SalesLead $lead): SalesLead
    {
        $lead->forceFill(['status' => LeadStatus::Qualified])->save();

        event(new LeadQualified($lead->refresh()));

        return $lead;
    }
}
