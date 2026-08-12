<?php

namespace App\Domains\Media\Data;

use App\Core\Data\BaseDTO;
use App\Core\Enums\MediaType;

final readonly class MediaMetadata extends BaseDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $fileName,
        public string $mimeType,
        public MediaType $type,
        public int $size,
        public string $url,
        public string $alt,
        public ?string $thumbUrl,
    ) {}

    public static function fromArray(array $data): static
    {
        $type = $data['type'] ?? MediaType::Other->value;

        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            fileName: (string) ($data['file_name'] ?? ''),
            mimeType: (string) ($data['mime_type'] ?? ''),
            type: $type instanceof MediaType ? $type : MediaType::from((string) $type),
            size: (int) ($data['size'] ?? 0),
            url: (string) ($data['url'] ?? ''),
            alt: (string) ($data['alt'] ?? ''),
            thumbUrl: isset($data['thumb_url']) ? (string) $data['thumb_url'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'file_name' => $this->fileName,
            'mime_type' => $this->mimeType,
            'type' => $this->type->value,
            'size' => $this->size,
            'url' => $this->url,
            'alt' => $this->alt,
            'thumb_url' => $this->thumbUrl,
        ];
    }
}
