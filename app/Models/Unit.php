<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'code',
        'name',
        'symbol',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'default_unit_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
