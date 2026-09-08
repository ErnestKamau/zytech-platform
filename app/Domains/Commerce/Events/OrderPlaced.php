<?php

namespace App\Domains\Commerce\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Order;

final class OrderPlaced extends BusinessEvent
{
    public function __construct(public Order $order) {}
}
