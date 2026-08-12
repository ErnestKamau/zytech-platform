<?php

namespace App\Domains\Media\Support;

use App\Core\Contracts\CacheStore;
use Illuminate\Support\Collection;

final class MediaCache
{
    public const FOLDER_TREE = 'media.folders.tree';

    public const COUNTS = 'media.counts';

    public const RECENT = 'media.recent';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::FOLDER_TREE,
            self::COUNTS,
            self::RECENT,
        ];
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

        $cache->forget($key);
        $value = $callback();
        $cache->put($key, $value, now()->addHour());

        return $value instanceof Collection ? $value : collect($value);
    }
}
