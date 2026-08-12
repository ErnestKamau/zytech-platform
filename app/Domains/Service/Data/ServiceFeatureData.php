<?php

namespace App\Domains\Service\Data;

use App\Core\Data\BaseDTO;

final readonly class ServiceFeatureData extends BaseDTO
{
    public function __construct(
        public string $title,
        public string $description,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            title: (string) ($data['title'] ?? ''),
            description: (string) ($data['description'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
