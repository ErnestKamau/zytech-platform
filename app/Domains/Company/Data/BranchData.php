<?php

namespace App\Domains\Company\Data;

use App\Core\Data\BaseDTO;
use App\Core\Enums\BranchType;

final readonly class BranchData extends BaseDTO
{
    public function __construct(
        public string $name,
        public BranchType $type,
        public ?string $address,
        public ?string $city,
        public ?string $county,
        public ?string $phone,
        public ?string $email,
        public bool $isPrimary,
    ) {}

    public static function fromArray(array $data): static
    {
        $type = $data['type'] ?? BranchType::Branch;
        if (! $type instanceof BranchType) {
            $type = BranchType::from((string) $type);
        }

        return new self(
            name: (string) $data['name'],
            type: $type,
            address: isset($data['address']) ? (string) $data['address'] : null,
            city: isset($data['city']) ? (string) $data['city'] : null,
            county: isset($data['county']) ? (string) $data['county'] : null,
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
            email: isset($data['email']) ? (string) $data['email'] : null,
            isPrimary: (bool) ($data['is_primary'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type->value,
            'address' => $this->address,
            'city' => $this->city,
            'county' => $this->county,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_primary' => $this->isPrimary,
        ];
    }
}
