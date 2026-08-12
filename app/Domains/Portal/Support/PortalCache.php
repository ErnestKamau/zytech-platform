<?php

namespace App\Domains\Portal\Support;

use App\Core\Contracts\CacheStore;

final class PortalCache
{
    public const DASHBOARD = 'portal.dashboard';

    public const ANNOUNCEMENTS = 'portal.announcements';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [self::DASHBOARD, self::ANNOUNCEMENTS];
    }

    public static function dashboardKey(string $clientId): string
    {
        return self::DASHBOARD.'.'.$clientId;
    }

    public static function forget(CacheStore $cache, ?string $clientId = null): void
    {
        $cache->forget(self::ANNOUNCEMENTS);

        if ($clientId !== null) {
            $cache->forget(self::dashboardKey($clientId));
        }
    }
}
