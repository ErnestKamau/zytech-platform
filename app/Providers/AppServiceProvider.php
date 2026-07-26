<?php

namespace App\Providers;

use App\Core\Contracts\CacheStore;
use App\Domains\Authentication\Events\AccountLocked;
use App\Domains\Authentication\Events\UserLoggedIn;
use App\Domains\Authentication\Events\UserLoggedOut;
use App\Domains\Authentication\Events\UserRegistered;
use App\Domains\Authentication\Listeners\BroadcastAuthenticationEvent;
use App\Domains\Authentication\Listeners\CreateNotifications;
use App\Domains\Authentication\Listeners\LogLogin;
use App\Domains\Authentication\Listeners\LogLogout;
use App\Domains\Authentication\Listeners\SendWelcomeEmail;
use App\Domains\User\Policies\PermissionPolicy;
use App\Domains\User\Policies\RolePolicy;
use App\Domains\User\Policies\UserPolicy;
use App\Infrastructure\Cache\ApplicationCache;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CacheStore::class, fn (): ApplicationCache => new ApplicationCache);
        $this->app->alias(CacheStore::class, ApplicationCache::class);
    }

    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);

        Gate::define('viewPulse', function (?User $user = null): bool {
            return $this->app->environment('local') || $user !== null;
        });

        Event::listen(UserRegistered::class, SendWelcomeEmail::class);
        Event::listen(UserLoggedIn::class, LogLogin::class);
        Event::listen(UserLoggedIn::class, BroadcastAuthenticationEvent::class);
        Event::listen(UserLoggedOut::class, LogLogout::class);
        Event::listen(UserLoggedOut::class, BroadcastAuthenticationEvent::class);
        Event::listen(AccountLocked::class, BroadcastAuthenticationEvent::class);
        Event::listen(AccountLocked::class, CreateNotifications::class);
    }
}
