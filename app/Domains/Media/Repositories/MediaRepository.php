<?php

namespace App\Domains\Media\Repositories;

use App\Core\Enums\MediaCollection;
use App\Models\Media;
use App\Models\MediaFolder;
use Illuminate\Support\Collection;

final class MediaRepository
{
    /**
     * @return Collection<int, Media>
     */
    public function recent(int $limit = 24): Collection
    {
        return Media::query()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, Media>
     */
    public function inFolder(string $folderId, ?MediaCollection $collection = null): Collection
    {
        $query = Media::query()
            ->where('model_type', MediaFolder::class)
            ->where('model_id', $folderId)
            ->orderBy('order_column');

        if ($collection !== null) {
            $query->where('collection_name', $collection->value);
        }

        return $query->get();
    }

    public function findBySiteKey(string $siteKey): ?Media
    {
        return Media::query()
            ->where('custom_properties->site_key', $siteKey)
            ->first();
    }

    /**
     * @return Collection<int, Media>
     */
    public function search(string $term, int $limit = 50): Collection
    {
        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';

        return Media::query()
            ->where(function ($query) use ($like): void {
                $query->where('name', 'ilike', $like)
                    ->orWhere('file_name', 'ilike', $like)
                    ->orWhere('mime_type', 'ilike', $like);
            })
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
