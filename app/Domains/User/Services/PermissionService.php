<?php

namespace App\Domains\User\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Services\BaseService;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Collection;

final class PermissionService extends BaseService
{
    public function __construct(
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return list<string>
     */
    public function namesFor(User $user): array
    {
        return $this->cache->remember(
            $this->userKey($user->id),
            now()->addHour(),
            fn (): array => $user->getAllPermissions()->pluck('name')->values()->all(),
        );
    }

    public function userCan(User $user, string $permission): bool
    {
        return in_array($permission, $this->namesFor($user), true);
    }

    public function forgetUserCache(User $user): void
    {
        $this->cache->forget($this->userKey($user->id));
        $this->cache->forget('roles.all');
        $this->cache->forget('permissions.all');
    }

    /**
     * @return Collection<int, Permission>
     */
    public function all(): Collection
    {
        return $this->cache->remember(
            'permissions.all',
            now()->addHour(),
            fn () => Permission::query()->orderBy('name')->get(),
        );
    }

    private function userKey(string $userId): string
    {
        return "user.{$userId}.permissions";
    }
}
