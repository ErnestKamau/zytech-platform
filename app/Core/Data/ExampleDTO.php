<?php

namespace App\Core\Data;

/**
 * Example DTO used by Phase 1 tests and as a template for domain DTOs.
 */
final readonly class ExampleDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            name: (string) $data['name'],
            email: (string) $data['email'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
