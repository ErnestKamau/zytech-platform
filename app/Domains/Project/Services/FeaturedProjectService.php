<?php

namespace App\Domains\Project\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Services\BaseService;
use App\Domains\Project\Data\ProjectData;
use App\Domains\Project\Repositories\ProjectRepository;
use App\Domains\Project\Support\ProjectCache;
use App\Models\Project;
use Illuminate\Support\Collection;

final class FeaturedProjectService extends BaseService
{
    public function __construct(
        private readonly ProjectRepository $projects,
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return Collection<int, ProjectData>
     */
    public function current(): Collection
    {
        return ProjectCache::rememberCollection(
            $this->cache,
            ProjectCache::FEATURED,
            fn (): Collection => $this->projects->featured()
                ->map(fn (Project $project): array => $this->toArray($project)),
        )->map(fn (mixed $row): ProjectData => $row instanceof ProjectData
            ? $row
            : ProjectData::fromArray(is_array($row) ? $row : []));
    }

    /**
     * @return array<string, mixed>
     */
    private function toArray(Project $project): array
    {
        $category = $project->category;

        return ProjectData::fromArray([
            ...$project->toArray(),
            'category_name' => $category?->name ?? '',
            'category_slug' => $category?->slug ?? '',
            'status_label' => $project->statusLabel(),
            'location_summary' => $project->locationSummary(),
        ])->toArray();
    }
}
