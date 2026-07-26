<?php

namespace App\Domains\Authentication\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Authentication\Services\AuthenticationService;
use App\Models\User;

final class LockAccount extends BaseAction
{
    public function __construct(
        private readonly AuthenticationService $authentication,
    ) {}

    public function handle(mixed ...$arguments): User
    {
        /** @var User $user */
        $user = $arguments[0];
        $reason = (string) ($arguments[1] ?? 'Account locked by administrator');

        return $this->authentication->lockAccount($user, $reason);
    }
}
