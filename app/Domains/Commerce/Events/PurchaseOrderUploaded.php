<?php

namespace App\Domains\Commerce\Events;

use App\Core\Events\BusinessEvent;
use App\Models\PurchaseOrder;

final class PurchaseOrderUploaded extends BusinessEvent
{
    public function __construct(public PurchaseOrder $purchaseOrder) {}
}
