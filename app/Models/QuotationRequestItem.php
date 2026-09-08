<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationRequestItem extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'quotation_request_id',
        'product_id',
        'product_variant_id',
        'description',
        'quantity',
        'unit_snapshot',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(QuotationRequest::class, 'quotation_request_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
