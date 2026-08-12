<?php

namespace App\Models;

use App\Core\Enums\PricingModel;
use App\Core\Enums\ServiceStatus;
use App\Core\Enums\ServiceType;
use App\Core\Enums\VisibilityStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use App\Core\Traits\HasPublishedState;
use App\Core\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends BaseModel
{
    use HasActivity;
    use HasPublishedState;
    use HasSlug;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'service_category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'icon_path',
        'image_key',
        'gallery_keys',
        'type',
        'status',
        'visibility',
        'pricing_model',
        'price_amount',
        'price_currency',
        'price_unit',
        'pricing_notes',
        'is_featured',
        'meta_title',
        'meta_description',
        'og_image_key',
        'published_at',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gallery_keys' => 'array',
            'type' => ServiceType::class,
            'status' => ServiceStatus::class,
            'visibility' => VisibilityStatus::class,
            'pricing_model' => PricingModel::class,
            'price_amount' => 'decimal:2',
            'is_featured' => 'boolean',
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
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function features(): HasMany
    {
        return $this->hasMany(ServiceFeature::class)->orderBy('sort_order');
    }

    public function processes(): HasMany
    {
        return $this->hasMany(ServiceProcess::class)->orderBy('sort_order');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ServiceFaq::class)->orderBy('sort_order');
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(ServiceStatistic::class)->orderBy('sort_order');
    }

    public function relatedProjects(): HasMany
    {
        return $this->hasMany(ServiceRelatedProject::class)->orderBy('sort_order');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_service')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_service')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }
}
