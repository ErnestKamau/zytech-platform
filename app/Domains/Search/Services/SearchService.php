<?php

namespace App\Domains\Search\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\ArticleStatus;
use App\Core\Enums\ProjectStatus;
use App\Core\Enums\ServiceStatus;
use App\Core\Enums\VisibilityStatus;
use App\Core\Services\BaseService;
use App\Domains\Search\Data\SearchResultData;
use App\Domains\Search\Support\SearchCache;
use App\Models\Article;
use App\Models\Client;
use App\Models\Project;
use App\Models\SearchQuery;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class SearchService extends BaseService
{
    public function __construct(private readonly CacheStore $cache) {}

    /**
     * @return Collection<int, SearchResultData>
     */
    public function search(string $term, string $context = 'website', ?User $user = null, int $limit = 20): Collection
    {
        $needle = trim($term);

        if ($needle === '' || mb_strlen($needle) < 2) {
            return collect();
        }

        $cacheKey = SearchCache::resultsKey($needle, $context);
        $cached = $this->cache->get($cacheKey);

        if (is_array($cached)) {
            return collect($cached)->map(fn (array $row): SearchResultData => SearchResultData::fromArray($row));
        }

        $results = collect()
            ->merge($this->searchProjects($needle, $limit))
            ->merge($this->searchServices($needle, $limit))
            ->merge($this->searchArticles($needle, $limit));

        if ($context === 'admin' && $user?->can('clients.view')) {
            $results = $results->merge($this->searchClients($needle, $limit));
        }

        $results = $results->unique(fn (SearchResultData $item): string => $item->type.':'.$item->id)
            ->take($limit)
            ->values();

        SearchQuery::query()->create([
            'query' => mb_substr($needle, 0, 255),
            'context' => $context,
            'user_id' => $user?->id,
            'result_count' => $results->count(),
        ]);

        $this->cache->forget(SearchCache::POPULAR);
        $this->cache->put($cacheKey, $results->map->toArray()->all(), now()->addMinutes(5));

        return $results;
    }

    /**
     * @return list<string>
     */
    public function suggestions(string $term, int $limit = 8): array
    {
        $needle = trim($term);

        if (mb_strlen($needle) < 2) {
            return $this->popular();
        }

        return SearchQuery::query()
            ->select('query')
            ->where('query', 'ilike', $needle.'%')
            ->groupBy('query')
            ->orderByRaw('count(*) desc')
            ->limit($limit)
            ->pluck('query')
            ->all();
    }

    /**
     * @return list<string>
     */
    public function popular(int $limit = 8): array
    {
        /** @var list<string> $popular */
        $popular = $this->cache->remember(
            SearchCache::POPULAR,
            now()->addHour(),
            fn (): array => SearchQuery::query()
                ->select('query', DB::raw('count(*) as total'))
                ->groupBy('query')
                ->orderByDesc('total')
                ->limit($limit)
                ->pluck('query')
                ->all(),
        );

        return $popular;
    }

    /**
     * @return Collection<int, SearchResultData>
     */
    private function searchProjects(string $needle, int $limit): Collection
    {
        return Project::query()
            ->where('status', ProjectStatus::Published)
            ->where('visibility', VisibilityStatus::Public)
            ->where(fn (Builder $query) => $this->matchText($query, $needle))
            ->limit($limit)
            ->get()
            ->map(fn (Project $project): SearchResultData => SearchResultData::fromArray([
                'type' => 'project',
                'id' => $project->id,
                'title' => $project->title,
                'url' => route('projects.show', $project->slug),
                'excerpt' => $project->excerpt,
            ]));
    }

    /**
     * @return Collection<int, SearchResultData>
     */
    private function searchServices(string $needle, int $limit): Collection
    {
        return Service::query()
            ->where('status', ServiceStatus::Published)
            ->where(fn (Builder $query) => $this->matchText($query, $needle))
            ->limit($limit)
            ->get()
            ->map(fn (Service $service): SearchResultData => SearchResultData::fromArray([
                'type' => 'service',
                'id' => $service->id,
                'title' => $service->title,
                'url' => route('services.show', $service->slug),
                'excerpt' => $service->excerpt,
            ]));
    }

    /**
     * @return Collection<int, SearchResultData>
     */
    private function searchArticles(string $needle, int $limit): Collection
    {
        return Article::query()
            ->where('status', ArticleStatus::Published)
            ->where('visibility', VisibilityStatus::Public)
            ->where(fn (Builder $query) => $this->matchText($query, $needle))
            ->limit($limit)
            ->get()
            ->map(fn (Article $article): SearchResultData => SearchResultData::fromArray([
                'type' => 'article',
                'id' => $article->id,
                'title' => $article->title,
                'url' => route('knowledge.show', $article->slug),
                'excerpt' => $article->excerpt,
            ]));
    }

    /**
     * @return Collection<int, SearchResultData>
     */
    private function searchClients(string $needle, int $limit): Collection
    {
        return Client::query()
            ->where(function (Builder $query) use ($needle): void {
                $query->where('name', 'ilike', "%{$needle}%")
                    ->orWhere('email', 'ilike', "%{$needle}%");
            })
            ->limit($limit)
            ->get()
            ->map(fn (Client $client): SearchResultData => SearchResultData::fromArray([
                'type' => 'client',
                'id' => $client->id,
                'title' => $client->name,
                'url' => url('/admin/clients'),
                'excerpt' => $client->email,
            ]));
    }

    private function matchText(Builder $query, string $needle): Builder
    {
        return $query->where(function (Builder $inner) use ($needle): void {
            $inner->whereRaw(
                "to_tsvector('english', coalesce(title,'') || ' ' || coalesce(excerpt,'')) @@ plainto_tsquery('english', ?)",
                [$needle],
            )->orWhere('title', 'ilike', "%{$needle}%")
                ->orWhere('excerpt', 'ilike', "%{$needle}%");
        });
    }
}
