<?php

namespace App\Domains\Knowledge\Support;

use App\Core\Contracts\CacheStore;
use Illuminate\Support\Collection;

final class KnowledgeCache
{
    public const PUBLISHED = 'knowledge.published';

    public const FEATURED = 'knowledge.featured';

    public const CATEGORIES = 'knowledge.categories';

    public const SHOW_PREFIX = 'knowledge.show.';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::PUBLISHED,
            self::FEATURED,
            self::CATEGORIES,
        ];
    }

    public static function show(string $slug): string
    {
        return self::SHOW_PREFIX.$slug;
    }

    /**
     * @param  callable(): Collection<int, mixed>  $callback
     * @return Collection<int, mixed>
     */
    public static function rememberCollection(CacheStore $cache, string $key, callable $callback): Collection
    {
        $cached = $cache->get($key);

        if ($cached instanceof Collection) {
            return $cached;
        }

        if (is_array($cached)) {
            return collect($cached);
        }

        $cache->forget($key);
        $value = $callback();
        $cache->put($key, $value, now()->addHour());

        return $value instanceof Collection ? $value : collect($value);
    }
}
