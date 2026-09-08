<?php

namespace App\Models;

use App\Core\Enums\FulfillmentMethod;
use App\Core\Enums\FulfillmentStatus;
use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fulfillment extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'order_id',
        'sales_order_id',
        'status',
        'method',
        'tracking_number',
        'carrier',
        'shipped_at',
        'delivered_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => FulfillmentStatus::class,
            'method' => FulfillmentMethod::class,
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
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
