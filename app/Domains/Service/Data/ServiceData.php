<?php

namespace App\Domains\Service\Data;

use App\Core\Data\BaseDTO;
use App\Core\Enums\PricingModel;
use App\Core\Enums\ServiceType;

final readonly class ServiceData extends BaseDTO
{
    /**
     * @param  list<string>  $galleryKeys
     * @param  list<ServiceFeatureData>  $features
     * @param  list<ServiceProcessData>  $processes
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $slug,
        public string $excerpt,
        public string $body,
        public string $iconPath,
        public ?string $imageKey,
        public array $galleryKeys,
        public ServiceType $type,
        public string $categoryName,
        public string $categorySlug,
        public bool $isFeatured,
        public PricingModel $pricingModel,
        public ?string $priceAmount,
        public string $priceCurrency,
        public ?string $priceUnit,
        public string $pricingNotes,
        public string $metaTitle,
        public string $metaDescription,
        public ?string $ogImageKey,
        public array $features,
        public array $processes,
    ) {}

    public static function fromArray(array $data): static
    {
        $type = $data['type'] ?? ServiceType::Design;
        if (! $type instanceof ServiceType) {
            $type = ServiceType::from((string) $type);
        }

        $pricing = $data['pricing_model'] ?? PricingModel::QuoteOnRequest;
        if (! $pricing instanceof PricingModel) {
            $pricing = PricingModel::from((string) $pricing);
        }

        $features = array_map(
            fn (mixed $feature): ServiceFeatureData => $feature instanceof ServiceFeatureData
                ? $feature
                : ServiceFeatureData::fromArray(is_array($feature) ? $feature : []),
            $data['features'] ?? [],
        );

        $processes = array_map(
            fn (mixed $process): ServiceProcessData => $process instanceof ServiceProcessData
                ? $process
                : ServiceProcessData::fromArray(is_array($process) ? $process : []),
            $data['processes'] ?? [],
        );

        $gallery = $data['gallery_keys'] ?? [];

        return new self(
            id: (string) ($data['id'] ?? ''),
            title: (string) ($data['title'] ?? ''),
            slug: (string) ($data['slug'] ?? ''),
            excerpt: (string) ($data['excerpt'] ?? ''),
            body: (string) ($data['body'] ?? ''),
            iconPath: (string) ($data['icon_path'] ?? ''),
            imageKey: isset($data['image_key']) && $data['image_key'] !== '' ? (string) $data['image_key'] : null,
            galleryKeys: is_array($gallery) ? array_values(array_map('strval', $gallery)) : [],
            type: $type,
            categoryName: (string) ($data['category_name'] ?? ''),
            categorySlug: (string) ($data['category_slug'] ?? ''),
            isFeatured: (bool) ($data['is_featured'] ?? false),
            pricingModel: $pricing,
            priceAmount: isset($data['price_amount']) && $data['price_amount'] !== null && $data['price_amount'] !== ''
                ? (string) $data['price_amount']
                : null,
            priceCurrency: (string) ($data['price_currency'] ?? 'KES'),
            priceUnit: isset($data['price_unit']) && $data['price_unit'] !== '' ? (string) $data['price_unit'] : null,
            pricingNotes: (string) ($data['pricing_notes'] ?? ''),
            metaTitle: (string) ($data['meta_title'] ?? ''),
            metaDescription: (string) ($data['meta_description'] ?? ''),
            ogImageKey: isset($data['og_image_key']) && $data['og_image_key'] !== '' ? (string) $data['og_image_key'] : null,
            features: array_values($features),
            processes: array_values($processes),
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
            'icon_path' => $this->iconPath,
            'image_key' => $this->imageKey,
            'gallery_keys' => $this->galleryKeys,
            'type' => $this->type->value,
            'category_name' => $this->categoryName,
            'category_slug' => $this->categorySlug,
            'is_featured' => $this->isFeatured,
            'pricing_model' => $this->pricingModel->value,
            'price_amount' => $this->priceAmount,
            'price_currency' => $this->priceCurrency,
            'price_unit' => $this->priceUnit,
            'pricing_notes' => $this->pricingNotes,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'og_image_key' => $this->ogImageKey,
            'features' => array_map(fn (ServiceFeatureData $feature): array => $feature->toArray(), $this->features),
            'processes' => array_map(fn (ServiceProcessData $process): array => $process->toArray(), $this->processes),
        ];
    }
}
