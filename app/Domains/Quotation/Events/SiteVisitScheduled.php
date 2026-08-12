<?php

namespace App\Domains\Quotation\Events;

use App\Core\Events\BusinessEvent;
use App\Models\SiteVisit;

final class SiteVisitScheduled extends BusinessEvent
{
    public function __construct(public SiteVisit $visit) {}
}
