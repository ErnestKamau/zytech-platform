<?php

namespace App\Domains\Project\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Services\BaseService;
use App\Domains\Project\Repositories\ProjectRepository;
use App\Domains\Project\Support\ProjectCache;
use App\Models\Project;
use Illuminate\Support\Collection;

final class ProjectMapService extends BaseService
{
    public function __construct(
        private readonly ProjectRepository $projects,
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return Collection<int, array{title: string, slug: string, county: ?string, city: ?string, latitude: float, longitude: float, image_key: ?string, category: string}>
     */
    public function markers(): Collection
    {
        return ProjectCache::rememberCollection(
            $this->cache,
            ProjectCache::MAP,
            fn (): Collection => $this->projects->withCoordinates()
                ->map(fn (Project $project): array => [
                    'title' => $project->title,
                    'slug' => $project->slug,
                    'county' => $project->county,
                    'city' => $project->city,
                    'latitude' => (float) $project->latitude,
                    'longitude' => (float) $project->longitude,
                    'image_key' => $project->image_key,
                    'category' => $project->category?->name ?? '',
                    'summary' => $project->locationSummary(),
                ]),
        );
    }
}
