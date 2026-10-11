<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceList extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'name',
        'code',
        'currency',
        'valid_from',
        'valid_until',
        'is_default',
        'active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_default' => 'boolean',
            'active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (PriceList $priceList): void {
            if ($priceList->is_default) {
                static::query()->whereKeyNot($priceList->getKey())->update(['is_default' => false]);
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceListItem::class);
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'price_list_client');
    }
}
