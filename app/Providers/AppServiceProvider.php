<?php

namespace App\Providers;

use App\Core\Contracts\CacheStore;
use App\Infrastructure\Cache\ApplicationCache;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CacheStore::class, fn (): ApplicationCache => new ApplicationCache);
        $this->app->alias(CacheStore::class, ApplicationCache::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('viewPulse', function (?User $user = null): bool {
            return $this->app->environment('local') || $user !== null;
        });
    }
}
