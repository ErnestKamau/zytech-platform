<?php

namespace App\Domains\Quotation\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Quotation;

final class QuotationRejected extends BusinessEvent
{
    public function __construct(public Quotation $quotation) {}
}
