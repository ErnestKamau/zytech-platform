<?php

namespace App\Domains\Authentication\Data;

use App\Core\Data\BaseDTO;
use App\Core\Enums\UserType;
use App\Core\ValueObjects\EmailAddress;

final readonly class RegisterUserData extends BaseDTO
{
    public function __construct(
        public string $name,
        public EmailAddress $email,
        public string $password,
        public UserType $type = UserType::Client,
        public ?string $phone = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            name: (string) $data['name'],
            email: new EmailAddress((string) $data['email']),
            password: (string) $data['password'],
            type: isset($data['type'])
                ? ($data['type'] instanceof UserType ? $data['type'] : UserType::from((string) $data['type']))
                : UserType::Client,
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => (string) $this->email,
            'password' => $this->password,
            'type' => $this->type->value,
            'phone' => $this->phone,
        ];
    }
}
