<?php

namespace App\Domains\Homepage\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Services\BaseService;
use App\Domains\Homepage\Data\HomepageSlideData;
use App\Domains\Homepage\Support\HomepageCache;
use App\Models\HomepageSlide;
use Illuminate\Support\Collection;

final class HomepageSlideService extends BaseService
{
    public function __construct(
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return Collection<int, HomepageSlideData>
     */
    public function active(): Collection
    {
        return HomepageCache::rememberCollection(
            $this->cache,
            HomepageCache::SLIDES,
            fn (): Collection => HomepageSlide::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (HomepageSlide $slide): array => $this->toData($slide)->toArray()),
        )->map(fn (mixed $row): HomepageSlideData => $row instanceof HomepageSlideData
            ? $row
            : HomepageSlideData::fromArray(is_array($row) ? $row : []));
    }

    public function forget(): void
    {
        $this->cache->forget(HomepageCache::SLIDES);
    }

    private function toData(HomepageSlide $slide): HomepageSlideData
    {
        return HomepageSlideData::fromArray([
            'id' => $slide->id,
            'headline' => $slide->headline,
            'eyebrow' => $slide->eyebrow,
            'alt_text' => $slide->alt_text,
            'cta_label' => $slide->cta_label,
            'cta_url' => $slide->cta_url,
            'media_type' => $slide->mediaType()?->value,
            'image_url' => $slide->imageUrl(),
            'video_url' => $slide->videoUrl(),
            'poster_url' => $slide->posterUrl(),
            'sort_order' => $slide->sort_order,
        ]);
    }
}
