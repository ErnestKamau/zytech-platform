<?php

namespace App\Domains\Authentication\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Authentication\Data\LoginData;
use App\Domains\Authentication\Data\LoginResult;
use App\Domains\Authentication\Services\AuthenticationService;

final class AuthenticateUser extends BaseAction
{
    public function __construct(
        private readonly AuthenticationService $authentication,
    ) {}

    public function handle(mixed ...$arguments): LoginResult
    {
        /** @var LoginData $data */
        $data = $arguments[0];

        return $this->authentication->authenticate($data);
    }
}
