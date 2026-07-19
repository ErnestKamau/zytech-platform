<?php

namespace Tests\Unit\Infrastructure;

use App\Core\Contracts\CacheStore;
use App\Infrastructure\Cache\ApplicationCache;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ApplicationCacheTest extends TestCase
{
    public function test_application_cache_is_bound_in_container(): void
    {
        $cache = $this->app->make(CacheStore::class);

        $this->assertInstanceOf(ApplicationCache::class, $cache);
    }

    public function test_application_cache_prefixes_keys(): void
    {
        Cache::flush();

        $cache = new ApplicationCache(prefix: 'zytech');

        $cache->put('settings.company', ['name' => 'Zytech'], 60);

        $this->assertSame(['name' => 'Zytech'], Cache::get('zytech.settings.company'));
        $this->assertSame(['name' => 'Zytech'], $cache->get('settings.company'));
    }
}
