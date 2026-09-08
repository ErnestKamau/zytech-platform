<?php

namespace App\Models;

use App\Core\Enums\InventoryMovementType;
use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'inventory_level_id',
        'type',
        'quantity',
        'reason',
        'reference_type',
        'reference_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => InventoryMovementType::class,
            'quantity' => 'decimal:2',
        ];
    }

    public function inventoryLevel(): BelongsTo
    {
        return $this->belongsTo(InventoryLevel::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
