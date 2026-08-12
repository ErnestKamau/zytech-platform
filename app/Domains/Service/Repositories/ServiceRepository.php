<?php

namespace App\Domains\Service\Repositories;

use App\Models\Service;
use Illuminate\Support\Collection;

final class ServiceRepository
{
    /**
     * @return Collection<int, Service>
     */
    public function published(): Collection
    {
        return Service::query()
            ->with('category')
            ->published()
            ->public()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
    }

    /**
     * @return Collection<int, Service>
     */
    public function featured(): Collection
    {
        return Service::query()
            ->with('category')
            ->published()
            ->public()
            ->featured()
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @return Collection<int, Service>
     */
    public function inCategory(string $categoryId): Collection
    {
        return Service::query()
            ->with('category')
            ->published()
            ->public()
            ->where('service_category_id', $categoryId)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
    }

    public function findPublishedBySlug(string $slug): ?Service
    {
        return Service::query()
            ->with([
                'category',
                'features',
                'processes',
                'faqs' => fn ($query) => $query->where('is_published', true),
                'statistics' => fn ($query) => $query->where('is_visible', true),
                'relatedProjects',
            ])
            ->published()
            ->public()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * @return Collection<int, Service>
     */
    public function related(Service $service, int $limit = 3): Collection
    {
        return Service::query()
            ->with('category')
            ->published()
            ->public()
            ->whereKeyNot($service->getKey())
            ->where(function ($query) use ($service): void {
                $query
                    ->where('service_category_id', $service->service_category_id)
                    ->orWhere('type', $service->type);
            })
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();
    }
}
