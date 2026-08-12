<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyStatistic extends BaseModel
{
    use HasActivity;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'label',
        'value',
        'is_visible',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
