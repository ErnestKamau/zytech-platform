<?php

namespace App\Domains\Configuration\Data;

use App\Core\Data\BaseDTO;

final readonly class SettingData extends BaseDTO
{
    public function __construct(
        public string $key,
        public mixed $value,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            key: (string) $data['key'],
            value: $data['value'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
        ];
    }
}
