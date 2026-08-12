<?php

namespace App\Domains\Project\Repositories;

use App\Models\Project;
use Illuminate\Support\Collection;

final class ProjectRepository
{
    /**
     * @return Collection<int, Project>
     */
    public function published(): Collection
    {
        return Project::query()
            ->with('category')
            ->published()
            ->public()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
    }

    /**
     * @return Collection<int, Project>
     */
    public function featured(): Collection
    {
        return Project::query()
            ->with('category')
            ->published()
            ->public()
            ->featured()
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @return Collection<int, Project>
     */
    public function inCategory(string $categoryId): Collection
    {
        return Project::query()
            ->with('category')
            ->published()
            ->public()
            ->where('project_category_id', $categoryId)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
    }

    /**
     * @return Collection<int, Project>
     */
    public function withCoordinates(): Collection
    {
        return Project::query()
            ->with('category')
            ->published()
            ->public()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('county')
            ->orderBy('title')
            ->get();
    }

    public function findPublishedBySlug(string $slug): ?Project
    {
        return Project::query()
            ->with([
                'category',
                'galleryItems',
                'milestones',
                'statistics' => fn ($query) => $query->where('is_visible', true),
                'beforeAfter',
                'progressUpdates' => fn ($query) => $query->where('is_published', true),
                'services' => fn ($query) => $query->published()->public(),
            ])
            ->published()
            ->public()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * @return Collection<int, Project>
     */
    public function related(Project $project, int $limit = 3): Collection
    {
        return Project::query()
            ->with('category')
            ->published()
            ->public()
            ->whereKeyNot($project->getKey())
            ->where(function ($query) use ($project): void {
                $query
                    ->where('project_category_id', $project->project_category_id)
                    ->orWhere('type', $project->type);
            })
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();
    }
}
