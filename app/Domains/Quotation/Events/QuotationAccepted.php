<?php

namespace App\Domains\Quotation\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Quotation;

final class QuotationAccepted extends BusinessEvent
{
    public function __construct(public Quotation $quotation) {}
}
