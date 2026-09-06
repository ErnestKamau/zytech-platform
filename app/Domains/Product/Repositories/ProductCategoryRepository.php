<?php

namespace App\Domains\Product\Repositories;

use App\Models\ProductCategory;
use Illuminate\Support\Collection;

final class ProductCategoryRepository
{
    /**
     * @return Collection<int, ProductCategory>
     */
    public function published(): Collection
    {
        return ProductCategory::query()
            ->where('is_published', true)
            ->withCount(['products as published_products_count' => function ($query): void {
                $query->published()->public();
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function findPublishedBySlug(string $slug): ?ProductCategory
    {
        return ProductCategory::query()
            ->where('is_published', true)
            ->where('slug', $slug)
            ->first();
    }
}
