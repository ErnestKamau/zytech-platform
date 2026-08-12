<?php

namespace App\Domains\Project\Repositories;

use App\Models\ProjectCategory;
use Illuminate\Support\Collection;

final class CategoryRepository
{
    /**
     * @return Collection<int, ProjectCategory>
     */
    public function published(): Collection
    {
        return ProjectCategory::query()
            ->where('is_published', true)
            ->withCount(['projects as published_projects_count' => function ($query): void {
                $query->published()->public();
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function findPublishedBySlug(string $slug): ?ProjectCategory
    {
        return ProjectCategory::query()
            ->where('is_published', true)
            ->where('slug', $slug)
            ->first();
    }
}
