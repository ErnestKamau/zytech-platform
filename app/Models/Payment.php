<?php

namespace App\Models;

use App\Core\Enums\PaymentStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'invoice_id',
        'order_id',
        'amount',
        'currency',
        'method',
        'status',
        'reference',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
