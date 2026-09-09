<?php

namespace App\Domains\Product\Data;

use App\Core\Data\BaseDTO;
use App\Core\Enums\PricingModel;
use App\Core\Enums\PurchaseMode;
use Illuminate\Support\Facades\Storage;

final readonly class ProductData extends BaseDTO
{
    /**
     * @param  list<string>  $galleryKeys
     * @param  array<string, mixed>  $specifications
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $slug,
        public ?string $sku,
        public string $excerpt,
        public string $body,
        public string $iconPath,
        public ?string $imageKey,
        public array $galleryKeys,
        public array $specifications,
        public ?string $unitOfMeasure,
        public PurchaseMode $purchaseMode,
        public string $categoryName,
        public string $categorySlug,
        public bool $isFeatured,
        public PricingModel $pricingModel,
        public ?string $priceAmount,
        public string $priceCurrency,
        public ?string $priceUnit,
        public string $pricingNotes,
        public bool $taxable,
        public ?int $stockDisplay,
        public string $metaTitle,
        public string $metaDescription,
        public ?string $ogImageKey,
    ) {}

    public static function fromArray(array $data): static
    {
        $pricing = $data['pricing_model'] ?? PricingModel::Fixed;
        if (! $pricing instanceof PricingModel) {
            $pricing = PricingModel::from((string) $pricing);
        }

        $mode = $data['purchase_mode'] ?? PurchaseMode::Quote;
        if (! $mode instanceof PurchaseMode) {
            $mode = PurchaseMode::from((string) $mode);
        }

        $gallery = $data['gallery_keys'] ?? [];
        $specs = $data['specifications'] ?? [];

        return new self(
            id: (string) ($data['id'] ?? ''),
            title: (string) ($data['title'] ?? ''),
            slug: (string) ($data['slug'] ?? ''),
            sku: isset($data['sku']) && $data['sku'] !== '' ? (string) $data['sku'] : null,
            excerpt: (string) ($data['excerpt'] ?? ''),
            body: (string) ($data['body'] ?? ''),
            iconPath: (string) ($data['icon_path'] ?? ''),
            imageKey: isset($data['image_key']) && $data['image_key'] !== '' ? (string) $data['image_key'] : null,
            galleryKeys: is_array($gallery) ? array_values(array_map('strval', $gallery)) : [],
            specifications: is_array($specs) ? $specs : [],
            unitOfMeasure: isset($data['unit_of_measure']) && $data['unit_of_measure'] !== ''
                ? (string) $data['unit_of_measure']
                : null,
            purchaseMode: $mode,
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
            taxable: (bool) ($data['taxable'] ?? true),
            stockDisplay: isset($data['stock_display']) ? (int) $data['stock_display'] : null,
            metaTitle: (string) ($data['meta_title'] ?? ''),
            metaDescription: (string) ($data['meta_description'] ?? ''),
            ogImageKey: isset($data['og_image_key']) && $data['og_image_key'] !== '' ? (string) $data['og_image_key'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'icon_path' => $this->iconPath,
            'image_key' => $this->imageKey,
            'gallery_keys' => $this->galleryKeys,
            'specifications' => $this->specifications,
            'unit_of_measure' => $this->unitOfMeasure,
            'purchase_mode' => $this->purchaseMode->value,
            'category_name' => $this->categoryName,
            'category_slug' => $this->categorySlug,
            'is_featured' => $this->isFeatured,
            'pricing_model' => $this->pricingModel->value,
            'price_amount' => $this->priceAmount,
            'price_currency' => $this->priceCurrency,
            'price_unit' => $this->priceUnit,
            'pricing_notes' => $this->pricingNotes,
            'taxable' => $this->taxable,
            'stock_display' => $this->stockDisplay,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'og_image_key' => $this->ogImageKey,
        ];
    }

    public function hasUploadedIcon(): bool
    {
        return $this->iconPath !== ''
            && (str_contains($this->iconPath, '/') || str_contains($this->iconPath, '.'));
    }

    public function iconUrl(): ?string
    {
        if (! $this->hasUploadedIcon()) {
            return null;
        }

        return Storage::disk('public')->url($this->iconPath);
    }
}
