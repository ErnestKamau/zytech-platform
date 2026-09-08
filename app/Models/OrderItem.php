<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Domain Invariant: order prices are commercial snapshots taken at the time
 * of purchase and must never change when the underlying product's price,
 * name, or SKU changes later.
 */
class OrderItem extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_title_snapshot',
        'sku_snapshot',
        'unit_snapshot',
        'quantity',
        'unit_price',
        'discount',
        'tax',
        'line_total',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'line_total' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Nullable and nullOnDelete: kept for admin convenience (e.g. jumping to
     * the current product), never relied on for historical figures.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
