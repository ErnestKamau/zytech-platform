<?php

namespace App\Domains\Commerce\Services;

use App\Core\Enums\FulfillmentMethod;
use App\Core\Enums\FulfillmentStatus;
use App\Core\Services\BaseService;
use App\Domains\Commerce\Exceptions\FulfillmentException;
use App\Models\Fulfillment;
use App\Models\Order;
use App\Models\SalesOrder;

final class FulfillmentService extends BaseService
{
    public function createForOrder(Order $order, ?FulfillmentMethod $method = null): Fulfillment
    {
        $order->loadMissing('fulfillment');

        if ($order->fulfillment !== null) {
            return $order->fulfillment;
        }

        return Fulfillment::query()->create([
            'order_id' => $order->id,
            'status' => FulfillmentStatus::Pending,
            'method' => $method ?? $order->fulfillment_method,
        ]);
    }

    public function createForSalesOrder(SalesOrder $salesOrder, ?FulfillmentMethod $method = null): Fulfillment
    {
        $existing = Fulfillment::query()->where('sales_order_id', $salesOrder->id)->first();

        if ($existing !== null) {
            return $existing;
        }

        return Fulfillment::query()->create([
            'sales_order_id' => $salesOrder->id,
            'status' => FulfillmentStatus::Pending,
            'method' => $method ?? FulfillmentMethod::Delivery,
        ]);
    }

    public function markPicking(Fulfillment $fulfillment): Fulfillment
    {
        $this->assertNotTerminal($fulfillment);

        $fulfillment->forceFill(['status' => FulfillmentStatus::Picking])->save();

        if ($fulfillment->order !== null) {
            app(OrderService::class)->markProcessing($fulfillment->order);
        }

        return $fulfillment->refresh();
    }

    /**
     * @param  array{tracking_number?: ?string, carrier?: ?string}  $details
     */
    public function markShipped(Fulfillment $fulfillment, array $details = []): Fulfillment
    {
        $this->assertNotTerminal($fulfillment);

        $fulfillment->forceFill([
            'status' => FulfillmentStatus::Shipped,
            'tracking_number' => $details['tracking_number'] ?? $fulfillment->tracking_number,
            'carrier' => $details['carrier'] ?? $fulfillment->carrier,
            'shipped_at' => now(),
        ])->save();

        if ($fulfillment->order !== null) {
            app(OrderService::class)->markReadyForFulfillment($fulfillment->order);
        }

        return $fulfillment->refresh();
    }

    public function markDelivered(Fulfillment $fulfillment): Fulfillment
    {
        $this->assertNotTerminal($fulfillment);

        $fulfillment->forceFill([
            'status' => FulfillmentStatus::Delivered,
            'delivered_at' => now(),
        ])->save();

        if ($fulfillment->order !== null) {
            app(OrderService::class)->complete($fulfillment->order);
        }

        return $fulfillment->refresh();
    }

    public function cancel(Fulfillment $fulfillment): Fulfillment
    {
        if ($fulfillment->status === FulfillmentStatus::Delivered) {
            throw FulfillmentException::alreadyDelivered();
        }

        $fulfillment->forceFill(['status' => FulfillmentStatus::Cancelled])->save();

        return $fulfillment->refresh();
    }

    private function assertNotTerminal(Fulfillment $fulfillment): void
    {
        if ($fulfillment->status->isTerminal()) {
            throw FulfillmentException::terminal();
        }
    }
}
