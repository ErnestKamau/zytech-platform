<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxRate extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'code',
        'name',
        'rate',
        'kra_tax_type_code',
        'is_default',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'is_default' => 'boolean',
            'active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (TaxRate $taxRate): void {
            if ($taxRate->is_default) {
                static::query()->whereKeyNot($taxRate->getKey())->update(['is_default' => false]);
            }
        });
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }
}
