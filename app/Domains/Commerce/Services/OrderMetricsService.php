<?php

namespace App\Domains\Commerce\Services;

use App\Core\Enums\OrderStatus;
use App\Core\Services\BaseService;
use App\Models\Order;

final class OrderMetricsService extends BaseService
{
    /**
     * @return array{
     *     today: int,
     *     mtd: int,
     *     pending_fulfilment: int,
     *     completed: int,
     *     cancelled: int
     * }
     */
    public function snapshot(): array
    {
        $todayStart = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        return [
            'today' => Order::query()
                ->where('placed_at', '>=', $todayStart)
                ->count(),
            'mtd' => Order::query()
                ->where('placed_at', '>=', $monthStart)
                ->count(),
            'pending_fulfilment' => Order::query()
                ->whereIn('status', [
                    OrderStatus::Pending,
                    OrderStatus::Confirmed,
                    OrderStatus::Processing,
                    OrderStatus::ReadyForFulfillment,
                ])
                ->count(),
            'completed' => Order::query()
                ->where('status', OrderStatus::Completed)
                ->count(),
            'cancelled' => Order::query()
                ->where('status', OrderStatus::Cancelled)
                ->count(),
        ];
    }
}
