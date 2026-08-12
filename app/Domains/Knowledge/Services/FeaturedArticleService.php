<?php

namespace App\Domains\Knowledge\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Services\BaseService;
use App\Domains\Knowledge\Data\ArticleData;
use App\Domains\Knowledge\Repositories\ArticleRepository;
use App\Domains\Knowledge\Support\KnowledgeCache;
use App\Models\Article;
use Illuminate\Support\Collection;

final class FeaturedArticleService extends BaseService
{
    public function __construct(
        private readonly ArticleRepository $articles,
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return Collection<int, ArticleData>
     */
    public function current(): Collection
    {
        return KnowledgeCache::rememberCollection(
            $this->cache,
            KnowledgeCache::FEATURED,
            fn (): Collection => $this->articles->featured()
                ->map(fn (Article $article): array => $this->toArray($article)),
        )->map(fn (mixed $row): ArticleData => $row instanceof ArticleData
            ? $row
            : ArticleData::fromArray(is_array($row) ? $row : []));
    }

    /**
     * @return array<string, mixed>
     */
    private function toArray(Article $article): array
    {
        $category = $article->category;
        $author = $article->author;

        return ArticleData::fromArray([
            ...$article->toArray(),
            'category_name' => $category?->name ?? '',
            'category_slug' => $category?->slug ?? '',
            'author_name' => $author?->name ?? '',
            'author_slug' => $author?->slug ?? '',
            'tags' => $article->tags->pluck('name')->all(),
        ])->toArray();
    }
}
