<?php

namespace App\Domains\User\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\User\Services\RoleService;
use App\Models\User;

final class AssignPermission extends BaseAction
{
    public function __construct(
        private readonly RoleService $roles,
    ) {}

    public function handle(mixed ...$arguments): User
    {
        /** @var User $user */
        $user = $arguments[0];
        /** @var string|list<string> $permissions */
        $permissions = $arguments[1];

        return $this->roles->assignPermission($user, $permissions);
    }
}
