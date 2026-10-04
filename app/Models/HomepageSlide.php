<?php

namespace App\Models;

use App\Core\Enums\ConversionType;
use App\Core\Enums\MediaCollection;
use App\Core\Enums\MediaType;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

class HomepageSlide extends BaseModel implements HasMedia
{
    use HasActivity;
    use InteractsWithMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'headline',
        'eyebrow',
        'alt_text',
        'cta_label',
        'cta_url',
        'is_active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::HomepageSlideMedia->value)
            ->useDisk('public')
            ->singleFile();

        $this->addMediaCollection(MediaCollection::HomepageSlidePoster->value)
            ->useDisk('public')
            ->singleFile()
            ->withResponsiveImages();
    }

    public function registerMediaConversions(?SpatieMedia $media = null): void
    {
        foreach (ConversionType::cases() as $conversion) {
            $registered = $this->addMediaConversion($conversion->value)
                ->width($conversion->width())
                ->performOnCollections(MediaCollection::HomepageSlidePoster->value)
                ->queued();

            if ($conversion === ConversionType::Webp) {
                $registered->format('webp');
            }

            if ($conversion === ConversionType::Thumb) {
                $registered->height($conversion->width())->sharpen(8)->nonQueued();
            }
        }
    }

    public function mediaType(): ?MediaType
    {
        return $this->getFirstMedia(MediaCollection::HomepageSlideMedia->value)?->mediaType();
    }

    public function videoUrl(): ?string
    {
        if ($this->mediaType() !== MediaType::Video) {
            return null;
        }

        $url = $this->getFirstMediaUrl(MediaCollection::HomepageSlideMedia->value);

        return $url !== '' ? $url : null;
    }

    public function imageUrl(): ?string
    {
        if ($this->mediaType() !== MediaType::Image) {
            return null;
        }

        $url = $this->getFirstMediaUrl(MediaCollection::HomepageSlideMedia->value);

        return $url !== '' ? $url : null;
    }

    public function posterUrl(): ?string
    {
        $url = $this->getFirstMediaUrl(MediaCollection::HomepageSlidePoster->value);

        return $url !== '' ? $url : null;
    }
}
