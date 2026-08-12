<?php

namespace App\Domains\Knowledge\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\ArticleStatus;
use App\Core\Enums\VisibilityStatus;
use App\Core\Services\BaseService;
use App\Domains\Knowledge\Data\ArticleData;
use App\Domains\Knowledge\Events\ArticleArchived;
use App\Domains\Knowledge\Events\ArticleCreated;
use App\Domains\Knowledge\Events\ArticlePublished;
use App\Domains\Knowledge\Events\ArticleUpdated;
use App\Domains\Knowledge\Events\FeaturedArticleChanged;
use App\Domains\Knowledge\Repositories\ArticleRepository;
use App\Domains\Knowledge\Repositories\CategoryRepository;
use App\Domains\Knowledge\Support\KnowledgeCache;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Collection;

final class KnowledgeCentreService extends BaseService
{
    public function __construct(
        private readonly ArticleRepository $articles,
        private readonly CategoryRepository $categories,
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return Collection<int, ArticleData>
     */
    public function published(?string $categorySlug = null): Collection
    {
        if ($categorySlug !== null && $categorySlug !== '') {
            $category = $this->categories->findPublishedBySlug($categorySlug);

            if ($category === null) {
                return collect();
            }

            return $this->articles->inCategory($category->id)
                ->map(fn (Article $article): ArticleData => $this->toData($article));
        }

        return KnowledgeCache::rememberCollection(
            $this->cache,
            KnowledgeCache::PUBLISHED,
            fn (): Collection => $this->articles->published()
                ->map(fn (Article $article): array => $this->toData($article)->toArray()),
        )->map(fn (mixed $row): ArticleData => $row instanceof ArticleData
            ? $row
            : ArticleData::fromArray(is_array($row) ? $row : []));
    }

    /**
     * @return Collection<int, ArticleCategory>
     */
    public function categories(): Collection
    {
        return KnowledgeCache::rememberCollection(
            $this->cache,
            KnowledgeCache::CATEGORIES,
            fn (): Collection => $this->categories->published(),
        );
    }

    /**
     * @return Collection<int, ArticleData>
     */
    public function search(string $term): Collection
    {
        return $this->articles->search($term)
            ->map(fn (Article $article): ArticleData => $this->toData($article));
    }

    public function findPublished(string $slug): ?ArticleData
    {
        $key = KnowledgeCache::show($slug);
        $cached = $this->cache->get($key);

        if (is_array($cached)) {
            return ArticleData::fromArray($cached);
        }

        $article = $this->articles->findPublishedBySlug($slug);

        if ($article === null) {
            return null;
        }

        $data = $this->toData($article, detailed: true);
        $this->cache->put($key, $data->toArray(), now()->addHour());

        return $data;
    }

    public function modelBySlug(string $slug): ?Article
    {
        return $this->articles->findPublishedBySlug($slug);
    }

    /**
     * @return Collection<int, ArticleData>
     */
    public function related(Article $article): Collection
    {
        return $this->articles->related($article)
            ->map(fn (Article $related): ArticleData => $this->toData($related));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Article
    {
        $article = Article::query()->create($attributes);

        event(new ArticleCreated($article->fresh(['category', 'author'])));
        $this->forget();

        return $article->refresh();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Article $article, array $attributes): Article
    {
        $article->fill($attributes)->save();

        event(new ArticleUpdated($article->fresh(['category', 'author'])));
        $this->forget($article->slug);

        return $article->refresh();
    }

    public function publish(Article $article): Article
    {
        $article->forceFill([
            'status' => ArticleStatus::Published,
            'visibility' => $article->visibility ?? VisibilityStatus::Public,
            'published_at' => $article->published_at ?? now(),
        ])->save();

        event(new ArticlePublished($article->fresh(['category', 'author'])));
        $this->forget($article->slug);

        return $article->refresh();
    }

    public function archive(Article $article): Article
    {
        $article->forceFill([
            'status' => ArticleStatus::Archived,
            'is_featured' => false,
        ])->save();

        event(new ArticleArchived($article->fresh(['category', 'author'])));
        $this->forget($article->slug);

        return $article->refresh();
    }

    public function feature(Article $article, bool $featured = true): Article
    {
        $article->forceFill(['is_featured' => $featured])->save();

        event(new FeaturedArticleChanged($article->fresh(['category', 'author'])));
        $this->forget($article->slug);

        return $article->refresh();
    }

    public function persisted(Article $article, bool $created = false): Article
    {
        $fresh = $article->fresh(['category', 'author']) ?? $article;

        event($created ? new ArticleCreated($fresh) : new ArticleUpdated($fresh));
        $this->forget($fresh->slug);

        return $fresh;
    }

    public function incrementViews(Article $article): void
    {
        $article->increment('view_count');
    }

    public function forget(?string $slug = null): void
    {
        foreach (KnowledgeCache::all() as $key) {
            $this->cache->forget($key);
        }

        if ($slug !== null && $slug !== '') {
            $this->cache->forget(KnowledgeCache::show($slug));
        }
    }

    private function toData(Article $article, bool $detailed = false): ArticleData
    {
        $category = $article->category;
        $author = $article->author;

        $payload = [
            ...$article->toArray(),
            'category_name' => $category?->name ?? '',
            'category_slug' => $category?->slug ?? '',
            'author_name' => $author?->name ?? '',
            'author_slug' => $author?->slug ?? '',
            'tags' => $article->relationLoaded('tags')
                ? $article->tags->pluck('name')->all()
                : [],
            'sections' => [],
        ];

        if ($detailed) {
            $payload['sections'] = $article->sections
                ->map(fn ($section): array => [
                    'heading' => $section->heading ?? '',
                    'body' => $section->body,
                    'image_key' => $section->image_key,
                    'sort_order' => $section->sort_order,
                ])
                ->all();
        }

        return ArticleData::fromArray($payload);
    }
}
