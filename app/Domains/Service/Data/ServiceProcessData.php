<?php

namespace App\Domains\Service\Data;

use App\Core\Data\BaseDTO;

final readonly class ServiceProcessData extends BaseDTO
{
    public function __construct(
        public string $title,
        public string $description,
        public int $sortOrder,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            title: (string) ($data['title'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            sortOrder: (int) ($data['sort_order'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'sort_order' => $this->sortOrder,
        ];
    }
}
