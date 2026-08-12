<?php

namespace App\Domains\Seo\Support;

use App\Core\Contracts\CacheStore;

final class SeoCache
{
    public const SITEMAP = 'seo.sitemap';

    public const ROBOTS = 'seo.robots';

    public static function forget(CacheStore $cache): void
    {
        $cache->forget(self::SITEMAP);
        $cache->forget(self::ROBOTS);
    }
}
