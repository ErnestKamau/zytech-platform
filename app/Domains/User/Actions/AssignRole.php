<?php

namespace App\Domains\User\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\User\Data\AssignRoleData;
use App\Domains\User\Services\RoleService;
use App\Models\User;

final class AssignRole extends BaseAction
{
    public function __construct(
        private readonly RoleService $roles,
    ) {}

    public function handle(mixed ...$arguments): User
    {
        /** @var AssignRoleData $data */
        $data = $arguments[0];

        return $this->roles->assign($data);
    }
}
