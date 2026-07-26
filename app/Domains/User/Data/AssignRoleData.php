<?php

namespace App\Domains\User\Data;

use App\Core\Data\BaseDTO;

final readonly class AssignRoleData extends BaseDTO
{
    /**
     * @param  list<string>  $roles
     */
    public function __construct(
        public string $userId,
        public array $roles,
    ) {}

    public static function fromArray(array $data): static
    {
        $roles = $data['roles'] ?? [];

        return new self(
            userId: (string) $data['user_id'],
            roles: array_values(array_map('strval', is_array($roles) ? $roles : [$roles])),
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'roles' => $this->roles,
        ];
    }
}
