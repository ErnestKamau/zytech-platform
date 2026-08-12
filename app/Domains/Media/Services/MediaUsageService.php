<?php

namespace App\Domains\Media\Services;

use App\Core\Services\BaseService;
use App\Models\Media;
use App\Models\MediaUsage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class MediaUsageService extends BaseService
{
    public function attach(Media $media, Model $usable, string $context = 'default'): MediaUsage
    {
        return MediaUsage::query()->updateOrCreate(
            [
                'media_id' => $media->getKey(),
                'usable_type' => $usable->getMorphClass(),
                'usable_id' => $usable->getKey(),
                'context' => $context,
            ],
        );
    }

    public function detach(Media $media, Model $usable, string $context = 'default'): void
    {
        MediaUsage::query()
            ->where('media_id', $media->getKey())
            ->where('usable_type', $usable->getMorphClass())
            ->where('usable_id', $usable->getKey())
            ->where('context', $context)
            ->delete();
    }

    /**
     * @return Collection<int, MediaUsage>
     */
    public function forMedia(Media $media): Collection
    {
        return $media->usages()->get();
    }
}
