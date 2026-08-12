<?php

namespace App\Models;

use App\Core\Enums\AwardCategory;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Award extends BaseModel
{
    use HasActivity;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'title',
        'category',
        'year',
        'issuer',
        'description',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => AwardCategory::class,
            'year' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
