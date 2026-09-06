<?php

namespace App\Domains\Commerce\Events;

use App\Core\Events\BusinessEvent;
use App\Models\SalesOrder;

final class SalesOrderCreated extends BusinessEvent
{
    public function __construct(public SalesOrder $salesOrder) {}
}
