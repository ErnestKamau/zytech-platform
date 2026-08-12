<?php

namespace App\Domains\Quotation\Events;

use App\Core\Events\BusinessEvent;
use App\Models\QuotationRequest;

final class QuotationRequestSubmitted extends BusinessEvent
{
    public function __construct(public QuotationRequest $request) {}
}
