<?php

namespace App\Domains\Company\Data;

use App\Core\Data\BaseDTO;

final readonly class TestimonialData extends BaseDTO
{
    public function __construct(
        public string $authorName,
        public string $quote,
        public ?string $authorRole = null,
        public ?string $companyName = null,
        public bool $isFeatured = false,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            authorName: (string) $data['author_name'],
            quote: (string) $data['quote'],
            authorRole: isset($data['author_role']) ? (string) $data['author_role'] : null,
            companyName: isset($data['company_name']) ? (string) $data['company_name'] : null,
            isFeatured: (bool) ($data['is_featured'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'author_name' => $this->authorName,
            'quote' => $this->quote,
            'author_role' => $this->authorRole,
            'company_name' => $this->companyName,
            'is_featured' => $this->isFeatured,
        ];
    }
}
