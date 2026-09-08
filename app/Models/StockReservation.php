<?php

namespace App\Models;

use App\Core\Enums\StockReservationStatus;
use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReservation extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'inventory_level_id',
        'order_id',
        'sales_order_id',
        'quantity',
        'status',
        'reserved_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => StockReservationStatus::class,
            'quantity' => 'decimal:2',
            'reserved_at' => 'datetime',
        ];
    }

    public function inventoryLevel(): BelongsTo
    {
        return $this->belongsTo(InventoryLevel::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
