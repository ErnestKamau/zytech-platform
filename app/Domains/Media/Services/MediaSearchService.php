<?php

namespace App\Domains\Media\Services;

use App\Core\Services\BaseService;
use App\Domains\Media\Repositories\MediaRepository;
use App\Models\Media;
use Illuminate\Support\Collection;

final class MediaSearchService extends BaseService
{
    public function __construct(
        private readonly MediaRepository $media,
    ) {}

    /**
     * @return Collection<int, Media>
     */
    public function search(string $term, int $limit = 50): Collection
    {
        $term = trim($term);

        if ($term === '') {
            return collect();
        }

        return $this->media->search($term, $limit);
    }
}
