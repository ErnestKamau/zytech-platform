<?php

namespace App\Domains\Search\Data;

use App\Core\Data\BaseDTO;

final readonly class SearchResultData extends BaseDTO
{
    public function __construct(
        public string $type,
        public string $id,
        public string $title,
        public string $url,
        public ?string $excerpt,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            type: (string) ($data['type'] ?? ''),
            id: (string) ($data['id'] ?? ''),
            title: (string) ($data['title'] ?? ''),
            url: (string) ($data['url'] ?? ''),
            excerpt: isset($data['excerpt']) ? (string) $data['excerpt'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->url,
            'excerpt' => $this->excerpt,
        ];
    }
}
