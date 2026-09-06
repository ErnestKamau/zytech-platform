<?php

namespace App\Domains\Commerce\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Invoice;

final class DraftInvoiceCreated extends BusinessEvent
{
    public function __construct(public Invoice $invoice) {}
}
