<?php

namespace App\Domains\Authentication\Data;

use App\Core\Data\BaseDTO;
use App\Core\ValueObjects\EmailAddress;

final readonly class LoginData extends BaseDTO
{
    public function __construct(
        public EmailAddress $email,
        public string $password,
        public bool $remember = false,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            email: new EmailAddress((string) $data['email']),
            password: (string) $data['password'],
            remember: (bool) ($data['remember'] ?? false),
            ipAddress: isset($data['ip_address']) ? (string) $data['ip_address'] : null,
            userAgent: isset($data['user_agent']) ? (string) $data['user_agent'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'email' => (string) $this->email,
            'password' => $this->password,
            'remember' => $this->remember,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
        ];
    }
}
