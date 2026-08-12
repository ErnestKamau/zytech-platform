<?php

namespace App\Domains\Quotation\Events;

use App\Core\Events\BusinessEvent;
use App\Models\SalesLead;

final class LeadCreated extends BusinessEvent
{
    public function __construct(public SalesLead $lead) {}
}
