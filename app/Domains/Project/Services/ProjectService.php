<?php

namespace App\Domains\Project\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\ProjectStatus;
use App\Core\Enums\VisibilityStatus;
use App\Core\Services\BaseService;
use App\Domains\Project\Data\ProjectData;
use App\Domains\Project\Events\FeaturedProjectChanged;
use App\Domains\Project\Events\ProjectArchived;
use App\Domains\Project\Events\ProjectCreated;
use App\Domains\Project\Events\ProjectPublished;
use App\Domains\Project\Events\ProjectUpdated;
use App\Domains\Project\Repositories\CategoryRepository;
use App\Domains\Project\Repositories\ProjectRepository;
use App\Domains\Project\Support\ProjectCache;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Support\Collection;

final class ProjectService extends BaseService
{
    public function __construct(
        private readonly ProjectRepository $projects,
        private readonly CategoryRepository $categories,
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return Collection<int, ProjectData>
     */
    public function published(?string $categorySlug = null): Collection
    {
        if ($categorySlug !== null && $categorySlug !== '') {
            $category = $this->categories->findPublishedBySlug($categorySlug);

            if ($category === null) {
                return collect();
            }

            return $this->projects->inCategory($category->id)
                ->map(fn (Project $project): ProjectData => $this->toData($project));
        }

        return ProjectCache::rememberCollection(
            $this->cache,
            ProjectCache::PUBLISHED,
            fn (): Collection => $this->projects->published()
                ->map(fn (Project $project): array => $this->toData($project)->toArray()),
        )->map(fn (mixed $row): ProjectData => $row instanceof ProjectData
            ? $row
            : ProjectData::fromArray(is_array($row) ? $row : []));
    }

    /**
     * @return Collection<int, ProjectCategory>
     */
    public function categories(): Collection
    {
        return ProjectCache::rememberCollection(
            $this->cache,
            ProjectCache::CATEGORIES,
            fn (): Collection => $this->categories->published(),
        );
    }

    public function findPublished(string $slug): ?ProjectData
    {
        $key = ProjectCache::show($slug);
        $cached = $this->cache->get($key);

        if (is_array($cached)) {
            return ProjectData::fromArray($cached);
        }

        $project = $this->projects->findPublishedBySlug($slug);

        if ($project === null) {
            return null;
        }

        $data = $this->toData($project, detailed: true);
        $this->cache->put($key, $data->toArray(), now()->addHour());

        return $data;
    }

    public function modelBySlug(string $slug): ?Project
    {
        return $this->projects->findPublishedBySlug($slug);
    }

    /**
     * @return Collection<int, ProjectData>
     */
    public function related(Project $project): Collection
    {
        return $this->projects->related($project)
            ->map(fn (Project $related): ProjectData => $this->toData($related));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Project
    {
        $project = Project::query()->create($attributes);

        event(new ProjectCreated($project->fresh(['category'])));
        $this->forget();

        return $project->refresh();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Project $project, array $attributes): Project
    {
        $project->fill($attributes)->save();

        event(new ProjectUpdated($project->fresh(['category'])));
        $this->forget($project->slug);

        return $project->refresh();
    }

    public function publish(Project $project): Project
    {
        $project->forceFill([
            'status' => ProjectStatus::Published,
            'visibility' => $project->visibility ?? VisibilityStatus::Public,
            'published_at' => $project->published_at ?? now(),
        ])->save();

        event(new ProjectPublished($project->fresh(['category'])));
        $this->forget($project->slug);

        return $project->refresh();
    }

    public function archive(Project $project): Project
    {
        $project->forceFill([
            'status' => ProjectStatus::Archived,
            'is_featured' => false,
        ])->save();

        event(new ProjectArchived($project->fresh(['category'])));
        $this->forget($project->slug);

        return $project->refresh();
    }

    public function feature(Project $project, bool $featured = true): Project
    {
        $project->forceFill(['is_featured' => $featured])->save();

        event(new FeaturedProjectChanged($project->fresh(['category'])));
        $this->forget($project->slug);

        return $project->refresh();
    }

    public function persisted(Project $project, bool $created = false): Project
    {
        $fresh = $project->fresh(['category']) ?? $project;

        event($created ? new ProjectCreated($fresh) : new ProjectUpdated($fresh));
        $this->forget($fresh->slug);

        return $fresh;
    }

    public function forget(?string $slug = null): void
    {
        foreach (ProjectCache::all() as $key) {
            $this->cache->forget($key);
        }

        if ($slug !== null && $slug !== '') {
            $this->cache->forget(ProjectCache::show($slug));
        }
    }

    private function toData(Project $project, bool $detailed = false): ProjectData
    {
        $category = $project->category;

        $payload = [
            ...$project->toArray(),
            'category_name' => $category?->name ?? '',
            'category_slug' => $category?->slug ?? '',
            'status_label' => $project->statusLabel(),
            'location_summary' => $project->locationSummary(),
            'milestones' => [],
        ];

        if ($detailed) {
            $payload['milestones'] = $project->milestones
                ->map(fn ($milestone): array => [
                    'title' => $milestone->title,
                    'description' => $milestone->description ?? '',
                    'status' => $milestone->status->value,
                    'sort_order' => $milestone->sort_order,
                ])
                ->all();
        }

        return ProjectData::fromArray($payload);
    }
}
