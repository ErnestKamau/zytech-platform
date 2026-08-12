<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use App\Core\Traits\HasSlug;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadSource extends BaseModel
{
    use HasActivity;
    use HasSlug;

    /** @var list<string> */
    protected $fillable = ['name', 'slug', 'description', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    protected function slugSourceAttribute(): string
    {
        return 'name';
    }

    public function leads(): HasMany
    {
        return $this->hasMany(SalesLead::class)->orderByDesc('created_at');
    }
}
