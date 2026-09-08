<?php

namespace App\Models;

use App\Core\Enums\CartStatus;
use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cart extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'client_id',
        'session_token',
        'status',
        'currency',
        'notes',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => CartStatus::class,
            'converted_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class)->orderBy('created_at');
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CartStatus::Active);
    }

    public function isEmpty(): bool
    {
        return $this->items()->doesntExist();
    }

    public function subtotal(): string
    {
        return (string) $this->items->sum(
            fn (CartItem $item): float => (float) $item->unit_price * (float) $item->quantity
        );
    }
}
