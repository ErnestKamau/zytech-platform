<?php

namespace App\Domains\Homepage\Data;

use App\Core\Data\BaseDTO;

final readonly class HomepageSlideData extends BaseDTO
{
    public function __construct(
        public string $id,
        public ?string $headline,
        public ?string $eyebrow,
        public string $altText,
        public ?string $ctaLabel,
        public ?string $ctaUrl,
        public ?string $mediaType,
        public ?string $imageUrl,
        public ?string $videoUrl,
        public ?string $posterUrl,
        public int $sortOrder,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            headline: $data['headline'] ?? null,
            eyebrow: $data['eyebrow'] ?? null,
            altText: (string) ($data['alt_text'] ?? ''),
            ctaLabel: $data['cta_label'] ?? null,
            ctaUrl: $data['cta_url'] ?? null,
            mediaType: $data['media_type'] ?? null,
            imageUrl: $data['image_url'] ?? null,
            videoUrl: $data['video_url'] ?? null,
            posterUrl: $data['poster_url'] ?? null,
            sortOrder: (int) ($data['sort_order'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'headline' => $this->headline,
            'eyebrow' => $this->eyebrow,
            'alt_text' => $this->altText,
            'cta_label' => $this->ctaLabel,
            'cta_url' => $this->ctaUrl,
            'media_type' => $this->mediaType,
            'image_url' => $this->imageUrl,
            'video_url' => $this->videoUrl,
            'poster_url' => $this->posterUrl,
            'sort_order' => $this->sortOrder,
        ];
    }
}
