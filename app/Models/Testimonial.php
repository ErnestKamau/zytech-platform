<?php

namespace App\Models;

use App\Core\Enums\CompanyStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use App\Core\Traits\HasPublishedState;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends BaseModel
{
    use HasActivity;
    use HasPublishedState;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'author_name',
        'author_role',
        'company_name',
        'quote',
        'is_featured',
        'status',
        'published_at',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'status' => CompanyStatus::class,
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
