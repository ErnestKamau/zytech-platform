<?php

namespace App\Models;

use App\Core\Enums\MediaCollection;
use App\Core\Enums\MediaType;
use App\Core\Enums\MilestoneStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProjectMilestone extends BaseModel implements HasMedia
{
    use HasActivity;
    use InteractsWithMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'stage',
        'status',
        'completed_on',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => MilestoneStatus::class,
            'completed_on' => 'date',
            'sort_order' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::MilestoneMedia->value)
            ->useDisk('public')
            ->singleFile();
    }

    public function mediaUrl(): ?string
    {
        $url = $this->getFirstMediaUrl(MediaCollection::MilestoneMedia->value);

        return $url !== '' ? $url : null;
    }

    public function mediaType(): ?MediaType
    {
        return $this->getFirstMedia(MediaCollection::MilestoneMedia->value)?->mediaType();
    }
}
