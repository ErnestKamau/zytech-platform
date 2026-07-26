<?php

namespace App\Domains\Authentication\Data;

use App\Core\Data\BaseDTO;
use App\Core\ValueObjects\EmailAddress;

final readonly class ResetPasswordData extends BaseDTO
{
    public function __construct(
        public EmailAddress $email,
        public string $token,
        public string $password,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            email: new EmailAddress((string) $data['email']),
            token: (string) $data['token'],
            password: (string) $data['password'],
        );
    }

    public function toArray(): array
    {
        return [
            'email' => (string) $this->email,
            'token' => $this->token,
            'password' => $this->password,
        ];
    }
}
