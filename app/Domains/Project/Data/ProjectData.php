<?php

namespace App\Domains\Project\Data;

use App\Core\Data\BaseDTO;
use App\Core\Enums\ConstructionStage;
use App\Core\Enums\ProjectType;

final readonly class ProjectData extends BaseDTO
{
    /**
     * @param  list<MilestoneData>  $milestones
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $slug,
        public string $excerpt,
        public string $body,
        public string $caseStudy,
        public ?string $imageKey,
        public ?string $videoKey,
        public ProjectType $type,
        public string $categoryName,
        public string $categorySlug,
        public bool $isFeatured,
        public ConstructionStage $constructionStage,
        public int $progressPercent,
        public string $statusLabel,
        public string $locationSummary,
        public ?string $county,
        public ?string $city,
        public ?float $latitude,
        public ?float $longitude,
        public ?int $completionYear,
        public string $metaTitle,
        public string $metaDescription,
        public ?string $ogImageKey,
        public array $milestones,
    ) {}

    public static function fromArray(array $data): static
    {
        $type = $data['type'] ?? ProjectType::Commercial;
        if (! $type instanceof ProjectType) {
            $type = ProjectType::from((string) $type);
        }

        $stage = $data['construction_stage'] ?? ConstructionStage::Planning;
        if (! $stage instanceof ConstructionStage) {
            $stage = ConstructionStage::from((string) $stage);
        }

        $milestones = array_map(
            fn (mixed $m): MilestoneData => $m instanceof MilestoneData
                ? $m
                : MilestoneData::fromArray(is_array($m) ? $m : []),
            $data['milestones'] ?? [],
        );

        return new self(
            id: (string) ($data['id'] ?? ''),
            title: (string) ($data['title'] ?? ''),
            slug: (string) ($data['slug'] ?? ''),
            excerpt: (string) ($data['excerpt'] ?? ''),
            body: (string) ($data['body'] ?? ''),
            caseStudy: (string) ($data['case_study'] ?? ''),
            imageKey: isset($data['image_key']) && $data['image_key'] !== '' ? (string) $data['image_key'] : null,
            videoKey: isset($data['video_key']) && $data['video_key'] !== '' ? (string) $data['video_key'] : null,
            type: $type,
            categoryName: (string) ($data['category_name'] ?? ''),
            categorySlug: (string) ($data['category_slug'] ?? ''),
            isFeatured: (bool) ($data['is_featured'] ?? false),
            constructionStage: $stage,
            progressPercent: (int) ($data['progress_percent'] ?? 0),
            statusLabel: (string) ($data['status_label'] ?? ''),
            locationSummary: (string) ($data['location_summary'] ?? ''),
            county: isset($data['county']) ? (string) $data['county'] : null,
            city: isset($data['city']) ? (string) $data['city'] : null,
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            completionYear: isset($data['completion_year']) ? (int) $data['completion_year'] : null,
            metaTitle: (string) ($data['meta_title'] ?? ''),
            metaDescription: (string) ($data['meta_description'] ?? ''),
            ogImageKey: isset($data['og_image_key']) && $data['og_image_key'] !== '' ? (string) $data['og_image_key'] : null,
            milestones: array_values($milestones),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'case_study' => $this->caseStudy,
            'image_key' => $this->imageKey,
            'video_key' => $this->videoKey,
            'type' => $this->type->value,
            'category_name' => $this->categoryName,
            'category_slug' => $this->categorySlug,
            'is_featured' => $this->isFeatured,
            'construction_stage' => $this->constructionStage->value,
            'progress_percent' => $this->progressPercent,
            'status_label' => $this->statusLabel,
            'location_summary' => $this->locationSummary,
            'county' => $this->county,
            'city' => $this->city,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'completion_year' => $this->completionYear,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'og_image_key' => $this->ogImageKey,
            'milestones' => array_map(fn (MilestoneData $m): array => $m->toArray(), $this->milestones),
        ];
    }
}
