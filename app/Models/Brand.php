<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends BaseModel
{
    use HasSlug;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected function slugSourceAttribute(): string
    {
        return 'name';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
