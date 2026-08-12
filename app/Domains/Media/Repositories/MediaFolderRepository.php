<?php

namespace App\Domains\Media\Repositories;

use App\Models\MediaFolder;
use Illuminate\Support\Collection;

final class MediaFolderRepository
{
    /**
     * @return Collection<int, MediaFolder>
     */
    public function roots(): Collection
    {
        return MediaFolder::query()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->with('children')
            ->get();
    }

    public function findBySlug(string $slug): ?MediaFolder
    {
        return MediaFolder::query()->where('slug', $slug)->first();
    }

    public function firstOrCreate(string $name, ?string $parentId = null): MediaFolder
    {
        return MediaFolder::query()->firstOrCreate(
            [
                'name' => $name,
                'parent_id' => $parentId,
            ],
            [
                'visibility' => 'public',
                'sort_order' => 0,
            ],
        );
    }
}
