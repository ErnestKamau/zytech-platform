<?php

namespace App\Models;

use App\Core\Enums\ArticleStatus;
use App\Core\Enums\ArticleType;
use App\Core\Enums\ReadingLevel;
use App\Core\Enums\VisibilityStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use App\Core\Traits\HasPublishedState;
use App\Core\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends BaseModel
{
    use HasActivity;
    use HasPublishedState;
    use HasSlug;

    /** @var list<string> */
    protected $fillable = [
        'article_category_id',
        'article_author_id',
        'title',
        'slug',
        'excerpt',
        'type',
        'status',
        'visibility',
        'reading_level',
        'reading_time_minutes',
        'image_key',
        'is_featured',
        'view_count',
        'meta_title',
        'meta_description',
        'og_image_key',
        'published_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => ArticleType::class,
            'status' => ArticleStatus::class,
            'visibility' => VisibilityStatus::class,
            'reading_level' => ReadingLevel::class,
            'reading_time_minutes' => 'integer',
            'is_featured' => 'boolean',
            'view_count' => 'integer',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', VisibilityStatus::Public);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(ArticleAuthor::class, 'article_author_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ArticleSection::class)->orderBy('sort_order');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ArticleFaq::class)->orderBy('sort_order');
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(ArticleDownload::class)->orderBy('sort_order');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ArticleTag::class, 'article_tag', 'article_id', 'article_tag_id');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'article_project')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'article_service')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }
}
