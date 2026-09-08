<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'product_id',
        'sku',
        'title',
        'attributes',
        'price_amount',
        'unit_id',
        'stock_display',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'price_amount' => 'decimal:2',
            'stock_display' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
