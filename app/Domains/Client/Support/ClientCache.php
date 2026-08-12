<?php

namespace App\Domains\Client\Support;

use App\Core\Contracts\CacheStore;

final class ClientCache
{
    public const DASHBOARD = 'clients.dashboard';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [self::DASHBOARD];
    }

    public static function forget(CacheStore $cache): void
    {
        foreach (self::all() as $key) {
            $cache->forget($key);
        }
    }
}
