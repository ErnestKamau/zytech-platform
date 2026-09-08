<?php

namespace App\Models;

use App\Core\Enums\SalesOrderStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SalesOrder extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'reference_number',
        'client_id',
        'quotation_id',
        'purchase_order_id',
        'source',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'billing_address',
        'delivery_address',
        'payment_terms',
        'notes',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => SalesOrderStatus::class,
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class)->orderBy('sort_order');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function fulfillment(): HasOne
    {
        return $this->hasOne(Fulfillment::class);
    }
}
