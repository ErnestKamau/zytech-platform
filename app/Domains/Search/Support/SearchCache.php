<?php

namespace App\Domains\Search\Support;

use App\Core\Contracts\CacheStore;

final class SearchCache
{
    public const SUGGESTIONS = 'search.suggestions';

    public const POPULAR = 'search.popular';

    public static function resultsKey(string $query, string $context): string
    {
        return 'search.results.'.md5($context.'|'.mb_strtolower(trim($query)));
    }

    public static function forget(CacheStore $cache): void
    {
        $cache->forget(self::SUGGESTIONS);
        $cache->forget(self::POPULAR);
    }
}
