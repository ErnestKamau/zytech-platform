<?php

namespace App\Domains\Authentication\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Authentication\Services\AuthenticationService;
use App\Models\User;

final class VerifyEmail extends BaseAction
{
    public function __construct(
        private readonly AuthenticationService $authentication,
    ) {}

    public function handle(mixed ...$arguments): User
    {
        /** @var User $user */
        $user = $arguments[0];

        return $this->authentication->verifyEmail($user);
    }
}
