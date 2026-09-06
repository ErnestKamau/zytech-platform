<?php

namespace Database\Seeders;

use App\Core\Enums\PricingModel;
use App\Core\Enums\ProductStatus;
use App\Core\Enums\PurchaseMode;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Product\Services\ProductService;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Cement & Binders', 'slug' => 'cement-binders', 'description' => 'Cement, mortar, and binding materials.', 'sort_order' => 1],
            ['name' => 'Tiles & Finishes', 'slug' => 'tiles-finishes', 'description' => 'Floor and wall tiles for residential and commercial finishes.', 'sort_order' => 2],
            ['name' => 'PPE & Site Safety', 'slug' => 'ppe-site-safety', 'description' => 'Protective gear for site crews.', 'sort_order' => 3],
            ['name' => 'Aggregates', 'slug' => 'aggregates', 'description' => 'Sand, ballast, and related aggregates.', 'sort_order' => 4],
        ];

        $categoryIds = [];
        foreach ($categories as $category) {
            $model = ProductCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    ...$category,
                    'is_published' => true,
                ],
            );
            $categoryIds[$category['slug']] = $model->id;
        }

        $products = [
            [
                'title' => 'Portland Cement 50kg',
                'slug' => 'portland-cement-50kg',
                'sku' => 'CEM-50KG',
                'category' => 'cement-binders',
                'excerpt' => 'Standard 50kg bags of Portland cement for general construction.',
                'body' => 'Suitable for foundations, slabs, columns, and general masonry. Available for known-price orders or larger negotiated quantities.',
                'unit_of_measure' => 'bag',
                'purchase_mode' => PurchaseMode::Both,
                'price_amount' => 950,
                'price_unit' => 'bag',
                'stock_display' => 400,
                'is_featured' => true,
                'specifications' => ['Pack size' => '50kg', 'Grade' => '42.5N'],
            ],
            [
                'title' => 'Ceramic Floor Tiles 60x60',
                'slug' => 'ceramic-floor-tiles-60x60',
                'sku' => 'TILE-6060',
                'category' => 'tiles-finishes',
                'excerpt' => 'Durable ceramic floor tiles for interiors.',
                'body' => 'Sold by the box. Colour and finish options can be confirmed via RFQ for project volumes.',
                'unit_of_measure' => 'box',
                'purchase_mode' => PurchaseMode::Both,
                'price_amount' => 2800,
                'price_unit' => 'box',
                'stock_display' => 120,
                'is_featured' => true,
                'specifications' => ['Size' => '60×60 cm', 'Coverage' => '~1.44 m² / box'],
            ],
            [
                'title' => 'Construction Safety Gloves',
                'slug' => 'construction-safety-gloves',
                'sku' => 'PPE-GLOVE',
                'category' => 'ppe-site-safety',
                'excerpt' => 'Heavy-duty gloves for site handling and general labour.',
                'body' => 'Sized pairs for site crews. Ideal for direct purchase alongside other PPE.',
                'unit_of_measure' => 'pair',
                'purchase_mode' => PurchaseMode::Buy,
                'price_amount' => 450,
                'price_unit' => 'pair',
                'stock_display' => 250,
                'is_featured' => false,
                'specifications' => ['Material' => 'Coated fabric', 'Use' => 'General site work'],
            ],
            [
                'title' => 'Washed River Sand',
                'slug' => 'washed-river-sand',
                'sku' => 'AGG-SAND',
                'category' => 'aggregates',
                'excerpt' => 'Washed river sand supplied by the tonne for plaster and concrete works.',
                'body' => 'Pricing and logistics depend on quantity and delivery location — request a quote for project supply.',
                'unit_of_measure' => 'tonne',
                'purchase_mode' => PurchaseMode::Quote,
                'price_amount' => null,
                'pricing_model' => PricingModel::QuoteOnRequest,
                'pricing_notes' => 'Delivery and volume discounts quoted per project.',
                'stock_display' => null,
                'is_featured' => true,
                'specifications' => ['Supply' => 'Bulk / tonne', 'Use' => 'Plaster & concrete'],
            ],
        ];

        foreach ($products as $product) {
            $categorySlug = $product['category'];
            unset($product['category']);

            Product::query()->updateOrCreate(
                ['slug' => $product['slug']],
                [
                    ...$product,
                    'product_category_id' => $categoryIds[$categorySlug],
                    'status' => ProductStatus::Published,
                    'visibility' => VisibilityStatus::Public,
                    'pricing_model' => $product['pricing_model'] ?? PricingModel::Fixed,
                    'price_currency' => 'KES',
                    'taxable' => true,
                    'published_at' => now(),
                    'sort_order' => 0,
                    'icon_path' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
                ],
            );
        }

        app(ProductService::class)->forget();
    }
}
