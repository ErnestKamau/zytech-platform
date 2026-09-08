<?php

namespace App\Domains\Commerce\Events;

use App\Core\Enums\OrderStatus;
use App\Core\Events\BusinessEvent;
use App\Models\Order;

final class OrderStatusChanged extends BusinessEvent
{
    public function __construct(
        public Order $order,
        public OrderStatus $from,
        public OrderStatus $to,
    ) {}
}
