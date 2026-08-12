<?php

namespace App\Models;

use App\Core\Enums\ConstructionStage;
use App\Core\Enums\ProjectStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\VisibilityStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use App\Core\Traits\HasPublishedState;
use App\Core\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends BaseModel
{
    use HasActivity;
    use HasPublishedState;
    use HasSlug;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'case_study',
        'image_key',
        'video_key',
        'type',
        'status',
        'visibility',
        'construction_stage',
        'progress_percent',
        'client_name',
        'completion_year',
        'started_on',
        'completed_on',
        'county',
        'city',
        'location_label',
        'latitude',
        'longitude',
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
            'type' => ProjectType::class,
            'status' => ProjectStatus::class,
            'visibility' => VisibilityStatus::class,
            'construction_stage' => ConstructionStage::class,
            'progress_percent' => 'integer',
            'completion_year' => 'integer',
            'started_on' => 'date',
            'completed_on' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
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
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function galleryItems(): HasMany
    {
        return $this->hasMany(ProjectGalleryItem::class)->orderBy('sort_order');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class)->orderBy('sort_order');
    }

    public function progressUpdates(): HasMany
    {
        return $this->hasMany(ProjectProgressUpdate::class)->orderByDesc('published_at');
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(ProjectStatistic::class)->orderBy('sort_order');
    }

    public function beforeAfter(): HasMany
    {
        return $this->hasMany(ProjectBeforeAfter::class)->orderBy('sort_order');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'project_service')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function statusLabel(): string
    {
        if ($this->construction_stage === ConstructionStage::Completed || $this->completed_on !== null) {
            return 'Completed';
        }

        if ($this->progress_percent > 0 || $this->construction_stage !== ConstructionStage::Planning) {
            return 'In progress';
        }

        return 'Planning';
    }

    public function locationSummary(): string
    {
        $parts = array_filter([
            $this->statusLabel(),
            $this->city ?: $this->county ?: $this->location_label,
        ]);

        return implode(' · ', $parts);
    }
}
