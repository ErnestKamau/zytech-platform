<?php

namespace App\Domains\Commerce\Services;

use App\Core\Enums\CartStatus;
use App\Core\Enums\FulfillmentMethod;
use App\Core\Enums\OrderPaymentStatus;
use App\Core\Enums\OrderStatus;
use App\Core\Services\BaseService;
use App\Domains\Commerce\Events\OrderCancelled;
use App\Domains\Commerce\Events\OrderPlaced;
use App\Domains\Commerce\Events\OrderStatusChanged;
use App\Domains\Commerce\Exceptions\CartException;
use App\Domains\Commerce\Exceptions\OrderException;
use App\Domains\Operations\Services\ActivityLogger;
use App\Models\Cart;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Support\Str;

final class OrderService extends BaseService
{
    public function __construct(
        private readonly InventoryService $inventory,
        private readonly ActivityLogger $activities,
    ) {}

    /**
     * @param  array{
     *     fulfillment_method?: FulfillmentMethod,
     *     contact_name?: ?string,
     *     contact_email?: ?string,
     *     contact_phone?: ?string,
     *     billing_address?: ?string,
     *     delivery_address?: ?string,
     *     notes?: ?string,
     * }  $details
     */
    public function checkout(Cart $cart, Client $client, array $details = []): Order
    {
        $cart->loadMissing('items.product');

        if ($cart->items->isEmpty()) {
            throw CartException::cartIsEmpty();
        }

        $order = $this->transaction(function () use ($cart, $client, $details): Order {
            $subtotal = $cart->items->sum(
                fn ($item): float => (float) $item->unit_price * (float) $item->quantity
            );

            $order = Order::query()->create([
                'order_number' => $this->nextOrderNumber(),
                'client_id' => $client->id,
                'cart_id' => $cart->id,
                'status' => OrderStatus::Pending,
                'payment_status' => OrderPaymentStatus::Unpaid,
                'fulfillment_method' => $details['fulfillment_method'] ?? FulfillmentMethod::Delivery,
                'currency' => $cart->currency,
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'shipping_amount' => 0,
                'total_amount' => $subtotal,
                'contact_name' => $details['contact_name'] ?? $client->name,
                'contact_email' => $details['contact_email'] ?? $client->email,
                'contact_phone' => $details['contact_phone'] ?? $client->phone,
                'billing_address' => $details['billing_address'] ?? null,
                'delivery_address' => $details['delivery_address'] ?? null,
                'notes' => $details['notes'] ?? null,
                'placed_at' => now(),
            ]);

            foreach ($cart->items->values() as $index => $item) {
                $product = $item->product;

                $order->items()->create([
                    'product_id' => $product?->id,
                    'product_title_snapshot' => $product?->title ?? 'Unknown product',
                    'sku_snapshot' => $product?->sku,
                    'unit_snapshot' => $product?->unit_of_measure,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => 0,
                    'tax' => 0,
                    'line_total' => (float) $item->unit_price * (float) $item->quantity,
                    'sort_order' => $index,
                ]);
            }

            $cart->update(['status' => CartStatus::Converted, 'converted_at' => now()]);

            $order = $order->fresh(['items']) ?? $order->refresh();

            $this->activities->log($order, 'order.placed', [
                'order_number' => $order->order_number,
                'client_id' => $client->id,
                'total_amount' => (float) $order->total_amount,
            ]);

            return $order;
        });

        event(new OrderPlaced($order));

        return $order->refresh();
    }

    /**
     * Confirming an order reserves stock for every line. Wrapped in a
     * transaction so that an oversell (InventoryException) rolls back the
     * status transition along with the reservation attempt.
     */
    public function confirm(Order $order): Order
    {
        $from = $order->status;

        $confirmed = $this->transaction(function () use ($order): Order {
            $confirmed = $this->transitionTo($order, OrderStatus::Confirmed, ['confirmed_at' => now()], dispatchEvent: false);

            $this->inventory->reserveForOrder($confirmed);
            app(FulfillmentService::class)->createForOrder($confirmed);

            return $confirmed->fresh(['fulfillment', 'items']) ?? $confirmed;
        });

        event(new OrderStatusChanged($confirmed, $from, OrderStatus::Confirmed));

        return $confirmed;
    }

    public function markProcessing(Order $order): Order
    {
        return $this->transitionTo($order, OrderStatus::Processing);
    }

    public function markReadyForFulfillment(Order $order): Order
    {
        return $this->transitionTo($order, OrderStatus::ReadyForFulfillment);
    }

    public function complete(Order $order): Order
    {
        return $this->transitionTo($order, OrderStatus::Completed);
    }

    public function cancel(Order $order): Order
    {
        if (! $order->isCancellable()) {
            throw OrderException::notCancellable();
        }

        $cancelled = $this->transaction(function () use ($order): Order {
            $from = $order->status;

            $order->forceFill([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
            ])->save();

            $this->activities->log($order, 'order.cancelled', [
                'order_number' => $order->order_number,
                'from_status' => $from->value,
            ]);

            return $order->refresh();
        });

        event(new OrderCancelled($cancelled));

        return $cancelled;
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function transitionTo(Order $order, OrderStatus $status, array $extra = [], bool $dispatchEvent = true): Order
    {
        $from = $order->status;

        $order = $this->transaction(function () use ($order, $status, $extra, $from): Order {
            $order->forceFill(array_merge(['status' => $status], $extra))->save();

            $this->activities->log($order, 'order.status_changed', [
                'order_number' => $order->order_number,
                'from_status' => $from->value,
                'to_status' => $status->value,
            ]);

            return $order->refresh();
        });

        if ($dispatchEvent) {
            event(new OrderStatusChanged($order, $from, $status));
        }

        return $order;
    }

    private function nextOrderNumber(): string
    {
        $date = now()->format('Ymd');

        do {
            $candidate = sprintf('ZORD-%s-%s', $date, Str::upper(Str::random(5)));
        } while (Order::query()->where('order_number', $candidate)->exists());

        return $candidate;
    }
}
