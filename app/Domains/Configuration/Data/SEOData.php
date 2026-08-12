<?php

namespace App\Domains\Configuration\Data;

use App\Core\Data\BaseDTO;

final readonly class SEOData extends BaseDTO
{
    public function __construct(
        public string $title,
        public string $description,
        public ?string $keywords = null,
        public ?string $ogImage = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            title: (string) ($data['seo.default_title'] ?? $data['title'] ?? 'Zytech Contractors'),
            description: (string) ($data['seo.default_description'] ?? $data['description'] ?? ''),
            keywords: isset($data['seo.keywords']) ? (string) $data['seo.keywords'] : ($data['keywords'] ?? null),
            ogImage: isset($data['seo.og_image']) ? (string) $data['seo.og_image'] : ($data['og_image'] ?? null),
        );
    }

    public function toArray(): array
    {
        return [
            'seo.default_title' => $this->title,
            'seo.default_description' => $this->description,
            'seo.keywords' => $this->keywords,
            'seo.og_image' => $this->ogImage,
        ];
    }
}
