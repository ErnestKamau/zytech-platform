<?php

namespace App\Domains\Company\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Partner;

final class PartnerAdded extends BusinessEvent
{
    public function __construct(public Partner $partner) {}
}
