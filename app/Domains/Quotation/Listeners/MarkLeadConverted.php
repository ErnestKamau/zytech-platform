<?php

namespace App\Domains\Quotation\Listeners;

use App\Core\Enums\LeadStatus;
use App\Core\Listeners\BaseListener;
use App\Domains\Quotation\Events\QuotationAccepted;
use App\Domains\Quotation\Services\LeadService;
use App\Infrastructure\Queue\QueueName;

final class MarkLeadConverted extends BaseListener
{
    public string $queue = QueueName::DEFAULT;

    public function __construct(private readonly LeadService $leads) {}

    public function handle(QuotationAccepted $event): void
    {
        $lead = $event->quotation->salesLead;

        if ($lead === null) {
            return;
        }

        if (! in_array($lead->status, [LeadStatus::New, LeadStatus::Contacted, LeadStatus::Qualified], true)) {
            return;
        }

        $this->leads->convert($lead);
    }
}
