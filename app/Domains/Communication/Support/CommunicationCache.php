<?php

namespace App\Domains\Communication\Support;

use App\Core\Contracts\CacheStore;

final class CommunicationCache
{
    public const ANNOUNCEMENTS = 'communication.announcements';

    public const TEMPLATES = 'communication.templates';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [self::ANNOUNCEMENTS, self::TEMPLATES];
    }

    public static function forget(CacheStore $cache): void
    {
        foreach (self::all() as $key) {
            $cache->forget($key);
        }
    }
}
