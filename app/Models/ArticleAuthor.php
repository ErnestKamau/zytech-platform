<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use App\Core\Traits\HasSlug;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleAuthor extends BaseModel
{
    use HasActivity;
    use HasSlug;

    /** @var list<string> */
    protected $fillable = ['name', 'slug', 'role', 'bio', 'photo_key', 'is_visible', 'sort_order'];

    protected function casts(): array
    {
        return ['is_visible' => 'boolean', 'sort_order' => 'integer'];
    }

    protected function slugSourceAttribute(): string
    {
        return 'name';
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class)->orderByDesc('published_at');
    }
}
