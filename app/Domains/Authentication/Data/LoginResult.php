<?php

namespace App\Domains\Authentication\Data;

use App\Core\Data\BaseDTO;
use App\Domains\Authentication\Enums\LoginStatus;
use App\Models\User;

final readonly class LoginResult extends BaseDTO
{
    public function __construct(
        public LoginStatus $status,
        public ?User $user = null,
    ) {}

    public static function fromArray(array $data): static
    {
        $status = $data['status'] ?? null;
        $status = $status instanceof LoginStatus
            ? $status
            : LoginStatus::from((string) $status);

        $user = $data['user'] ?? null;

        if ($user !== null && ! $user instanceof User) {
            $user = User::query()->find($user);
        }

        return new self(
            status: $status,
            user: $user instanceof User ? $user : null,
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
            'user_id' => $this->user?->id,
        ];
    }
}
