<?php

namespace App\Domains\User\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\User\Data\UpdateProfileData;
use App\Domains\User\Services\ProfileService;
use App\Models\User;

final class UpdateProfile extends BaseAction
{
    public function __construct(
        private readonly ProfileService $profiles,
    ) {}

    public function handle(mixed ...$arguments): User
    {
        /** @var User $user */
        $user = $arguments[0];
        /** @var UpdateProfileData $data */
        $data = $arguments[1];

        return $this->profiles->update($user, $data);
    }
}
