<?php

namespace App\Domains\Service\Repositories;

use App\Models\ServiceCategory;
use Illuminate\Support\Collection;

final class CategoryRepository
{
    /**
     * @return Collection<int, ServiceCategory>
     */
    public function published(): Collection
    {
        return ServiceCategory::query()
            ->where('is_published', true)
            ->withCount(['services as published_services_count' => function ($query): void {
                $query->published()->public();
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function findPublishedBySlug(string $slug): ?ServiceCategory
    {
        return ServiceCategory::query()
            ->where('is_published', true)
            ->where('slug', $slug)
            ->first();
    }
}
