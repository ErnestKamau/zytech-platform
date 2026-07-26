<?php

namespace App\Domains\User\Data;

use App\Core\Data\BaseDTO;
use App\Core\ValueObjects\EmailAddress;

final readonly class UpdateProfileData extends BaseDTO
{
    public function __construct(
        public string $name,
        public EmailAddress $email,
        public ?string $phone = null,
        /** @var array<string, mixed>|null */
        public ?array $preferences = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            name: (string) $data['name'],
            email: new EmailAddress((string) $data['email']),
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
            preferences: isset($data['preferences']) && is_array($data['preferences'])
                ? $data['preferences']
                : null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => (string) $this->email,
            'phone' => $this->phone,
            'preferences' => $this->preferences,
        ];
    }
}
