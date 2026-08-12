<?php

namespace App\Domains\Knowledge\Repositories;

use App\Models\Article;
use Illuminate\Support\Collection;

final class ArticleRepository
{
    /**
     * @return Collection<int, Article>
     */
    public function published(): Collection
    {
        return Article::query()
            ->with(['category', 'author', 'tags'])
            ->published()
            ->public()
            ->orderByDesc('published_at')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
    }

    /**
     * @return Collection<int, Article>
     */
    public function featured(): Collection
    {
        return Article::query()
            ->with(['category', 'author', 'tags'])
            ->published()
            ->public()
            ->featured()
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->get();
    }

    /**
     * @return Collection<int, Article>
     */
    public function inCategory(string $categoryId): Collection
    {
        return Article::query()
            ->with(['category', 'author', 'tags'])
            ->published()
            ->public()
            ->where('article_category_id', $categoryId)
            ->orderByDesc('published_at')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
    }

    /**
     * @return Collection<int, Article>
     */
    public function search(string $term, int $limit = 12): Collection
    {
        $needle = trim($term);

        if ($needle === '') {
            return collect();
        }

        return Article::query()
            ->with(['category', 'author', 'tags'])
            ->published()
            ->public()
            ->where(function ($query) use ($needle): void {
                $query
                    ->where('title', 'ilike', "%{$needle}%")
                    ->orWhere('excerpt', 'ilike', "%{$needle}%");
            })
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    public function findPublishedBySlug(string $slug): ?Article
    {
        return Article::query()
            ->with([
                'category',
                'author',
                'tags',
                'sections',
                'faqs' => fn ($query) => $query->where('is_published', true),
                'downloads',
                'projects' => fn ($query) => $query->published()->public()->with('category'),
                'services' => fn ($query) => $query->published()->public()->with('category'),
            ])
            ->published()
            ->public()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * @return Collection<int, Article>
     */
    public function related(Article $article, int $limit = 3): Collection
    {
        $tagIds = $article->tags->pluck('id');

        return Article::query()
            ->with(['category', 'author', 'tags'])
            ->published()
            ->public()
            ->whereKeyNot($article->getKey())
            ->where(function ($query) use ($article, $tagIds): void {
                $query->where('article_category_id', $article->article_category_id);

                if ($tagIds->isNotEmpty()) {
                    $query->orWhereHas('tags', fn ($tagQuery) => $tagQuery->whereIn('article_tags.id', $tagIds));
                }
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }
}
