<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Partner extends BaseModel
{
    use HasActivity;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'website',
        'logo_url',
        'description',
        'is_published',
        'archived_at',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'archived_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }
}
