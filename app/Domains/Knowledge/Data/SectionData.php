<?php

namespace App\Domains\Knowledge\Data;

use App\Core\Data\BaseDTO;

final readonly class SectionData extends BaseDTO
{
    public function __construct(
        public string $heading,
        public string $body,
        public ?string $imageKey,
        public int $sortOrder,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            heading: (string) ($data['heading'] ?? ''),
            body: (string) ($data['body'] ?? ''),
            imageKey: isset($data['image_key']) && $data['image_key'] !== '' ? (string) $data['image_key'] : null,
            sortOrder: (int) ($data['sort_order'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'heading' => $this->heading,
            'body' => $this->body,
            'image_key' => $this->imageKey,
            'sort_order' => $this->sortOrder,
        ];
    }
}
