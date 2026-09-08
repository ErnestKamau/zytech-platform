<?php

namespace App\Models;

use App\Core\Enums\FulfillmentMethod;
use App\Core\Enums\OrderPaymentStatus;
use App\Core\Enums\OrderStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * A direct, "buy now" e-commerce order placed against the product catalog.
 *
 * This is intentionally separate from SalesOrder, which represents an order
 * that originated from the B2B RFQ -> Quotation -> PurchaseOrder pipeline.
 * The two share the "order" concept but have different origins, different
 * approval paths, and different lifecycles, so keeping them as distinct
 * models avoids overloading SalesOrder with nullable, direct-purchase-only
 * columns and conditional logic.
 */
class Order extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'order_number',
        'client_id',
        'cart_id',
        'status',
        'payment_status',
        'fulfillment_method',
        'currency',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'shipping_amount',
        'total_amount',
        'contact_name',
        'contact_email',
        'contact_phone',
        'billing_address',
        'delivery_address',
        'notes',
        'placed_at',
        'confirmed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => OrderPaymentStatus::class,
            'fulfillment_method' => FulfillmentMethod::class,
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'placed_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderByDesc('created_at');
    }

    public function fulfillment(): HasOne
    {
        return $this->hasOne(Fulfillment::class);
    }

    public function scopeForClient(Builder $query, Client $client): Builder
    {
        return $query->where('client_id', $client->id);
    }

    /**
     * Domain Invariant: orders cannot be edited after fulfillment begins.
     */
    public function isEditable(): bool
    {
        return ! $this->status->fulfillmentStarted();
    }

    public function isCancellable(): bool
    {
        return $this->status->isCancellable();
    }
}
