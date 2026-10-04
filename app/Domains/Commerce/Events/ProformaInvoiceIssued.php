<?php

namespace App\Domains\Commerce\Events;

use App\Core\Events\BusinessEvent;
use App\Models\ProformaInvoice;

final class ProformaInvoiceIssued extends BusinessEvent
{
    public function __construct(public ProformaInvoice $proformaInvoice) {}
}
