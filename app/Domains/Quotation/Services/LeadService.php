<?php

namespace App\Domains\Quotation\Services;

use App\Core\Enums\LeadStatus;
use App\Core\Enums\PriorityLevel;
use App\Core\Services\BaseService;
use App\Domains\Operations\Services\ActivityLogger;
use App\Domains\Quotation\Events\LeadCreated;
use App\Domains\Quotation\Events\LeadQualified;
use App\Models\SalesLead;
use RuntimeException;

final class LeadService extends BaseService
{
    public function __construct(private readonly ActivityLogger $activities) {}

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
        $this->assertTransitionable($lead, [LeadStatus::New, LeadStatus::Contacted]);

        $lead->forceFill(['status' => LeadStatus::Qualified])->save();

        event(new LeadQualified($lead->refresh()));

        return $lead;
    }

    public function contact(SalesLead $lead): SalesLead
    {
        $this->assertTransitionable($lead, [LeadStatus::New]);

        return $this->transition($lead, LeadStatus::Contacted);
    }

    public function disqualify(SalesLead $lead): SalesLead
    {
        $this->assertTransitionable($lead, [LeadStatus::New, LeadStatus::Contacted, LeadStatus::Qualified]);

        return $this->transition($lead, LeadStatus::Unqualified);
    }

    public function archive(SalesLead $lead): SalesLead
    {
        $this->assertTransitionable($lead, [LeadStatus::New, LeadStatus::Contacted, LeadStatus::Qualified, LeadStatus::Unqualified]);

        return $this->transition($lead, LeadStatus::Archived);
    }

    public function convert(SalesLead $lead): SalesLead
    {
        $this->assertTransitionable($lead, [LeadStatus::New, LeadStatus::Contacted, LeadStatus::Qualified]);

        return $this->transition($lead, LeadStatus::Converted);
    }

    /**
     * @param  list<LeadStatus>  $allowedFrom
     */
    private function assertTransitionable(SalesLead $lead, array $allowedFrom): void
    {
        if (! in_array($lead->status, $allowedFrom, true)) {
            throw new RuntimeException("Lead \"{$lead->full_name}\" cannot move from {$lead->status->label()} to this stage.");
        }
    }

    private function transition(SalesLead $lead, LeadStatus $status): SalesLead
    {
        $from = $lead->status->value;

        $lead->forceFill(['status' => $status])->save();

        $this->activities->log($lead, 'lead.'.$status->value, ['from' => $from]);

        return $lead->refresh();
    }
}
