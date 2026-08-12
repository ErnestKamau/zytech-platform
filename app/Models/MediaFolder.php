<?php

namespace App\Models;

use App\Core\Enums\ConversionType;
use App\Core\Enums\MediaCollection;
use App\Core\Enums\MediaVisibility;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use App\Core\Traits\HasSlug;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

class MediaFolder extends BaseModel implements HasMedia
{
    use HasActivity;
    use HasSlug;
    use InteractsWithMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'visibility',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visibility' => MediaVisibility::class,
            'sort_order' => 'integer',
        ];
    }

    protected function slugSourceAttribute(): string
    {
        return 'name';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function registerMediaCollections(): void
    {
        foreach (MediaCollection::cases() as $collection) {
            $library = $this->addMediaCollection($collection->value)
                ->useDisk($collection->isPrivate() ? 'local' : 'public');

            if ($collection->acceptsConversions()) {
                $library->withResponsiveImages();
            }
        }
    }

    public function registerMediaConversions(?SpatieMedia $media = null): void
    {
        $imageCollections = array_map(
            fn (MediaCollection $collection): string => $collection->value,
            array_filter(MediaCollection::cases(), fn (MediaCollection $collection): bool => $collection->acceptsConversions()),
        );

        foreach (ConversionType::cases() as $conversion) {
            $registered = $this->addMediaConversion($conversion->value)
                ->width($conversion->width())
                ->performOnCollections(...$imageCollections)
                ->queued();

            if ($conversion === ConversionType::Webp) {
                $registered->format('webp');
            }

            if ($conversion === ConversionType::Thumb) {
                $registered->height($conversion->width())->sharpen(8)->nonQueued();
            }
        }
    }
}
