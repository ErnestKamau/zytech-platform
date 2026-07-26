<?php

namespace App\Domains\Authentication\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Authentication\Services\AuthenticationService;
use App\Models\User;

final class LogoutUser extends BaseAction
{
    public function __construct(
        private readonly AuthenticationService $authentication,
    ) {}

    public function handle(mixed ...$arguments): mixed
    {
        /** @var User|null $user */
        $user = $arguments[0] ?? null;

        $this->authentication->logout($user);

        return null;
    }
}
