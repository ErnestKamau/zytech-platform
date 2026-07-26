<?php

namespace App\Domains\Authentication\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Authentication\Data\ResetPasswordData;
use App\Domains\Authentication\Services\AuthenticationService;

final class ResetPassword extends BaseAction
{
    public function __construct(
        private readonly AuthenticationService $authentication,
    ) {}

    public function handle(mixed ...$arguments): string
    {
        /** @var ResetPasswordData $data */
        $data = $arguments[0];

        return $this->authentication->resetPassword($data);
    }
}
