<?php

namespace App\Domains\Knowledge\Data;

use App\Core\Data\BaseDTO;
use App\Core\Enums\ArticleType;
use App\Core\Enums\ReadingLevel;

final readonly class ArticleData extends BaseDTO
{
    /**
     * @param  list<string>  $tags
     * @param  list<SectionData>  $sections
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $slug,
        public string $excerpt,
        public ArticleType $type,
        public string $categoryName,
        public string $categorySlug,
        public string $authorName,
        public string $authorSlug,
        public ReadingLevel $readingLevel,
        public int $readingTimeMinutes,
        public ?string $imageKey,
        public bool $isFeatured,
        public string $metaTitle,
        public string $metaDescription,
        public ?string $ogImageKey,
        public array $tags,
        public array $sections,
    ) {}

    public static function fromArray(array $data): static
    {
        $type = $data['type'] ?? ArticleType::Guide;
        if (! $type instanceof ArticleType) {
            $type = ArticleType::from((string) $type);
        }

        $level = $data['reading_level'] ?? ReadingLevel::Beginner;
        if (! $level instanceof ReadingLevel) {
            $level = ReadingLevel::from((string) $level);
        }

        $sections = array_map(
            fn (mixed $section): SectionData => $section instanceof SectionData
                ? $section
                : SectionData::fromArray(is_array($section) ? $section : []),
            $data['sections'] ?? [],
        );

        return new self(
            id: (string) ($data['id'] ?? ''),
            title: (string) ($data['title'] ?? ''),
            slug: (string) ($data['slug'] ?? ''),
            excerpt: (string) ($data['excerpt'] ?? ''),
            type: $type,
            categoryName: (string) ($data['category_name'] ?? ''),
            categorySlug: (string) ($data['category_slug'] ?? ''),
            authorName: (string) ($data['author_name'] ?? ''),
            authorSlug: (string) ($data['author_slug'] ?? ''),
            readingLevel: $level,
            readingTimeMinutes: (int) ($data['reading_time_minutes'] ?? 1),
            imageKey: isset($data['image_key']) && $data['image_key'] !== '' ? (string) $data['image_key'] : null,
            isFeatured: (bool) ($data['is_featured'] ?? false),
            metaTitle: (string) ($data['meta_title'] ?? ''),
            metaDescription: (string) ($data['meta_description'] ?? ''),
            ogImageKey: isset($data['og_image_key']) && $data['og_image_key'] !== '' ? (string) $data['og_image_key'] : null,
            tags: array_values($data['tags'] ?? []),
            sections: array_values($sections),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'type' => $this->type->value,
            'category_name' => $this->categoryName,
            'category_slug' => $this->categorySlug,
            'author_name' => $this->authorName,
            'author_slug' => $this->authorSlug,
            'reading_level' => $this->readingLevel->value,
            'reading_time_minutes' => $this->readingTimeMinutes,
            'image_key' => $this->imageKey,
            'is_featured' => $this->isFeatured,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'og_image_key' => $this->ogImageKey,
            'tags' => $this->tags,
            'sections' => array_map(fn (SectionData $section): array => $section->toArray(), $this->sections),
        ];
    }
}
