<?php

namespace App\Domains\Product\Repositories;

use App\Models\Product;
use Illuminate\Support\Collection;

final class ProductRepository
{
    /**
     * @return Collection<int, Product>
     */
    public function published(): Collection
    {
        return Product::query()
            ->with('category')
            ->published()
            ->public()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
    }

    /**
     * @return Collection<int, Product>
     */
    public function featured(): Collection
    {
        return Product::query()
            ->with('category')
            ->published()
            ->public()
            ->featured()
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @return Collection<int, Product>
     */
    public function inCategory(string $categoryId): Collection
    {
        return Product::query()
            ->with('category')
            ->published()
            ->public()
            ->where('product_category_id', $categoryId)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
    }

    public function findPublishedBySlug(string $slug): ?Product
    {
        return Product::query()
            ->with('category')
            ->published()
            ->public()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * @return Collection<int, Product>
     */
    public function related(Product $product, int $limit = 3): Collection
    {
        return Product::query()
            ->with('category')
            ->published()
            ->public()
            ->whereKeyNot($product->getKey())
            ->where('product_category_id', $product->product_category_id)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();
    }
}
