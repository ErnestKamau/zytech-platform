<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryLevel extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'warehouse_code',
        'on_hand',
        'reserved',
    ];

    protected function casts(): array
    {
        return [
            'on_hand' => 'decimal:2',
            'reserved' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class)->orderByDesc('created_at');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(StockReservation::class);
    }

    public function available(): float
    {
        return (float) $this->on_hand - (float) $this->reserved;
    }
}
