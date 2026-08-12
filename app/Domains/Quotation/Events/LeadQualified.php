<?php

namespace App\Domains\Quotation\Events;

use App\Core\Events\BusinessEvent;
use App\Models\SalesLead;

final class LeadQualified extends BusinessEvent
{
    public function __construct(public SalesLead $lead) {}
}
