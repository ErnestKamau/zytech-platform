<?php

namespace App\Domains\User\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Services\BaseService;
use App\Domains\User\Data\AssignRoleData;
use App\Domains\User\Events\PermissionAssigned;
use App\Domains\User\Events\RoleAssigned;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;

final class RoleService extends BaseService
{
    public function __construct(
        private readonly CacheStore $cache,
        private readonly PermissionService $permissions,
    ) {}

    public function assign(AssignRoleData $data): User
    {
        return $this->transaction(function () use ($data): User {
            /** @var User $user */
            $user = User::query()->findOrFail($data->userId);
            $user->syncRoles($data->roles);

            $this->permissions->forgetUserCache($user);

            event(new RoleAssigned($user, $data->roles));

            return $user->refresh();
        });
    }

    public function assignPermission(User $user, string|array $permissions): User
    {
        $list = is_array($permissions) ? $permissions : [$permissions];

        $user->givePermissionTo($list);
        $this->permissions->forgetUserCache($user);

        event(new PermissionAssigned($user, $list));

        return $user->refresh();
    }

    /**
     * @return Collection<int, Role>
     */
    public function all(): Collection
    {
        return $this->cache->remember('roles.all', now()->addHour(), fn () => Role::query()->orderBy('name')->get());
    }
}
