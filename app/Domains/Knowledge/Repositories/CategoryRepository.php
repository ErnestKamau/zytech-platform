<?php

namespace App\Domains\Knowledge\Repositories;

use App\Models\ArticleCategory;
use Illuminate\Support\Collection;

final class CategoryRepository
{
    /**
     * @return Collection<int, ArticleCategory>
     */
    public function published(): Collection
    {
        return ArticleCategory::query()
            ->where('is_published', true)
            ->withCount(['articles as published_articles_count' => function ($query): void {
                $query->published()->public();
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function findPublishedBySlug(string $slug): ?ArticleCategory
    {
        return ArticleCategory::query()
            ->where('is_published', true)
            ->where('slug', $slug)
            ->first();
    }
}
