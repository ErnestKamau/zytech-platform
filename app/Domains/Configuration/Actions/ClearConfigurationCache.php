<?php

namespace App\Domains\Configuration\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Contracts\CacheStore;
use App\Core\Enums\NavigationLocation;
use App\Domains\Configuration\Support\ConfigurationCache;

final class ClearConfigurationCache extends BaseAction
{
    public function __construct(
        private readonly CacheStore $cache,
    ) {}

    public function handle(mixed ...$arguments): mixed
    {
        foreach (ConfigurationCache::settingsKeys() as $key) {
            $this->cache->forget($key);
        }

        $this->cache->forget(ConfigurationCache::FEATURE_FLAGS);

        foreach (NavigationLocation::cases() as $location) {
            $this->cache->forget(ConfigurationCache::navigation($location->value));
        }

        return null;
    }
}
