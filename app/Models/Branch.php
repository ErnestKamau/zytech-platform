<?php

namespace App\Models;

use App\Core\Enums\BranchType;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Branch extends BaseModel
{
    use HasActivity;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'type',
        'address',
        'city',
        'county',
        'phone',
        'email',
        'latitude',
        'longitude',
        'is_primary',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => BranchType::class,
            'is_primary' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
            'sort_order' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
