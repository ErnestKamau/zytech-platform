<?php

namespace App\Models;

use App\Core\Enums\NavigationLocation;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationMenu extends BaseModel
{
    use HasActivity;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'location',
        'is_published',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'location' => NavigationLocation::class,
            'is_published' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(NavigationItem::class)->orderBy('sort_order');
    }

    public function visibleItems(): HasMany
    {
        return $this->items()->where('is_visible', true)->whereNull('parent_id');
    }
}
