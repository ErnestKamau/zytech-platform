<?php

namespace App\Domains\Seo\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\ArticleStatus;
use App\Core\Enums\ProjectStatus;
use App\Core\Enums\ServiceStatus;
use App\Core\Enums\VisibilityStatus;
use App\Core\Services\BaseService;
use App\Domains\Seo\Support\SeoCache;
use App\Models\Article;
use App\Models\Project;
use App\Models\Service;

final class SitemapService extends BaseService
{
    public function __construct(private readonly CacheStore $cache) {}

    /**
     * @return list<array{loc: string, lastmod: ?string, changefreq: string, priority: string}>
     */
    public function urls(): array
    {
        /** @var list<array{loc: string, lastmod: ?string, changefreq: string, priority: string}> $urls */
        $urls = $this->cache->remember(
            SeoCache::SITEMAP,
            now()->addHour(),
            function (): array {
                $entries = [
                    $this->entry(route('home'), now()->toAtomString(), 'daily', '1.0'),
                    $this->entry(route('about'), null, 'monthly', '0.7'),
                    $this->entry(route('services.index'), null, 'weekly', '0.8'),
                    $this->entry(route('projects.index'), null, 'weekly', '0.8'),
                    $this->entry(route('knowledge.index'), null, 'weekly', '0.8'),
                    $this->entry(route('quote.index'), null, 'monthly', '0.6'),
                    $this->entry(route('contact'), null, 'monthly', '0.6'),
                    $this->entry(route('search'), null, 'weekly', '0.5'),
                ];

                foreach (Service::query()->where('status', ServiceStatus::Published)->get(['slug', 'updated_at']) as $service) {
                    $entries[] = $this->entry(route('services.show', $service->slug), $service->updated_at?->toAtomString(), 'weekly', '0.7');
                }

                foreach (
                    Project::query()
                        ->where('status', ProjectStatus::Published)
                        ->where('visibility', VisibilityStatus::Public)
                        ->get(['slug', 'updated_at']) as $project
                ) {
                    $entries[] = $this->entry(route('projects.show', $project->slug), $project->updated_at?->toAtomString(), 'weekly', '0.7');
                }

                foreach (
                    Article::query()
                        ->where('status', ArticleStatus::Published)
                        ->where('visibility', VisibilityStatus::Public)
                        ->get(['slug', 'updated_at']) as $article
                ) {
                    $entries[] = $this->entry(route('knowledge.show', $article->slug), $article->updated_at?->toAtomString(), 'weekly', '0.6');
                }

                return $entries;
            },
        );

        return $urls;
    }

    public function xml(): string
    {
        $items = collect($this->urls())->map(function (array $url): string {
            $lastmod = $url['lastmod'] ? '<lastmod>'.e($url['lastmod']).'</lastmod>' : '';

            return '<url><loc>'.e($url['loc']).'</loc>'.$lastmod
                .'<changefreq>'.e($url['changefreq']).'</changefreq>'
                .'<priority>'.e($url['priority']).'</priority></url>';
        })->implode('');

        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            .$items
            .'</urlset>';
    }

    public function forget(): void
    {
        $this->cache->forget(SeoCache::SITEMAP);
    }

    /**
     * @return array{loc: string, lastmod: ?string, changefreq: string, priority: string}
     */
    private function entry(string $loc, ?string $lastmod, string $changefreq, string $priority): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}
