<?php

namespace App\Domains\Commerce\Services;

use App\Core\Enums\InventoryMovementType;
use App\Core\Enums\StockReservationStatus;
use App\Core\Services\BaseService;
use App\Domains\Commerce\Exceptions\InventoryException;
use App\Models\InventoryLevel;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockReservation;
use Illuminate\Support\Facades\Auth;

final class InventoryService extends BaseService
{
    /**
     * Find or create the inventory level tracking a given product/variant.
     */
    public function levelFor(Product $product, ?ProductVariant $variant = null, ?string $warehouseCode = null): InventoryLevel
    {
        return InventoryLevel::query()->firstOrCreate([
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'warehouse_code' => $warehouseCode,
        ], [
            'on_hand' => 0,
            'reserved' => 0,
        ]);
    }

    public function available(InventoryLevel $level): float
    {
        return $level->available();
    }

    /**
     * Manually adjust on-hand stock up or down (e.g. stocktake correction).
     */
    public function adjust(InventoryLevel $level, float $quantity, ?string $reason = null): InventoryLevel
    {
        return $this->transaction(function () use ($level, $quantity, $reason): InventoryLevel {
            $level->forceFill(['on_hand' => (float) $level->on_hand + $quantity])->save();

            $this->recordMovement($level, InventoryMovementType::Adjust, $quantity, $reason);

            return $level->refresh();
        });
    }

    /**
     * Receive new stock into a level (e.g. goods received from a supplier).
     */
    public function receive(InventoryLevel $level, float $quantity, ?string $reason = null): InventoryLevel
    {
        if ($quantity <= 0) {
            throw InventoryException::invalidQuantity();
        }

        return $this->transaction(function () use ($level, $quantity, $reason): InventoryLevel {
            $level->forceFill(['on_hand' => (float) $level->on_hand + $quantity])->save();

            $this->recordMovement($level, InventoryMovementType::In, $quantity, $reason);

            return $level->refresh();
        });
    }

    /**
     * Reserve stock for every line on a direct Order, throwing if any line
     * would oversell the available quantity.
     */
    public function reserveForOrder(Order $order): void
    {
        $order->loadMissing('items.product', 'items.variant');

        $this->transaction(function () use ($order): void {
            foreach ($order->items as $item) {
                if ($item->product === null) {
                    continue;
                }

                $level = $this->levelFor($item->product, $item->variant);
                $quantity = (float) $item->quantity;

                if ($level->available() < $quantity) {
                    throw InventoryException::insufficientStock($item->product, $quantity, $level->available());
                }

                $level->forceFill(['reserved' => (float) $level->reserved + $quantity])->save();

                StockReservation::query()->create([
                    'inventory_level_id' => $level->id,
                    'order_id' => $order->id,
                    'quantity' => $quantity,
                    'status' => StockReservationStatus::Active,
                    'reserved_at' => now(),
                ]);

                $this->recordMovement($level->refresh(), InventoryMovementType::Reserve, $quantity, 'Order '.$order->order_number, $order);
            }
        });
    }

    public function release(StockReservation $reservation): StockReservation
    {
        if ($reservation->status !== StockReservationStatus::Active) {
            throw InventoryException::reservationNotActive();
        }

        return $this->transaction(function () use ($reservation): StockReservation {
            $level = $reservation->inventoryLevel;
            $level->forceFill(['reserved' => max((float) $level->reserved - (float) $reservation->quantity, 0)])->save();

            $reservation->forceFill(['status' => StockReservationStatus::Released])->save();

            $this->recordMovement($level->refresh(), InventoryMovementType::Release, (float) $reservation->quantity, 'Reservation released');

            return $reservation->refresh();
        });
    }

    /**
     * Consume an active reservation, permanently removing the stock from
     * on-hand once it has actually shipped/been used.
     */
    public function consume(StockReservation $reservation): StockReservation
    {
        if ($reservation->status !== StockReservationStatus::Active) {
            throw InventoryException::reservationNotActive();
        }

        return $this->transaction(function () use ($reservation): StockReservation {
            $level = $reservation->inventoryLevel;
            $quantity = (float) $reservation->quantity;

            $level->forceFill([
                'reserved' => max((float) $level->reserved - $quantity, 0),
                'on_hand' => max((float) $level->on_hand - $quantity, 0),
            ])->save();

            $reservation->forceFill(['status' => StockReservationStatus::Consumed])->save();

            $this->recordMovement($level->refresh(), InventoryMovementType::Out, $quantity, 'Reservation consumed');

            return $reservation->refresh();
        });
    }

    private function recordMovement(
        InventoryLevel $level,
        InventoryMovementType $type,
        float $quantity,
        ?string $reason = null,
        ?object $reference = null,
    ): InventoryMovement {
        return InventoryMovement::query()->create([
            'inventory_level_id' => $level->id,
            'type' => $type,
            'quantity' => $quantity,
            'reason' => $reason,
            'reference_type' => $reference !== null ? $reference::class : null,
            'reference_id' => $reference?->getKey(),
            'user_id' => Auth::id(),
        ]);
    }
}
