<?php

namespace App\Domains\Authentication\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Authentication\Data\RegisterUserData;
use App\Domains\Authentication\Services\AuthenticationService;
use App\Models\User;

final class RegisterUser extends BaseAction
{
    public function __construct(
        private readonly AuthenticationService $authentication,
    ) {}

    public function handle(mixed ...$arguments): User
    {
        /** @var RegisterUserData $data */
        $data = $arguments[0];

        return $this->authentication->register($data);
    }
}
