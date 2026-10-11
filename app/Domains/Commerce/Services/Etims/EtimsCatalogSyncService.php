<?php

namespace App\Domains\Commerce\Services\Etims;

use App\Domains\Company\Services\CompanyService;
use App\Models\EtimsSetting;
use App\Models\Product;
use RuntimeException;

final class EtimsCatalogSyncService
{
    public function __construct(
        private readonly EtimsHttpClient $http,
        private readonly CompanyService $companies,
    ) {}

    public function syncProduct(Product $product): Product
    {
        if (blank($product->kra_classification_code)) {
            throw new RuntimeException("Product \"{$product->title}\" is missing a KRA item classification code.");
        }

        $response = $this->registerItem($product->title, $product->kra_classification_code);

        $product->forceFill([
            'kra_item_code' => $response['injongecode'] ?? $response['sid'] ?? null,
            'kra_synced_at' => now(),
        ])->save();

        return $product->refresh();
    }

    public function syncDefaultServiceItem(EtimsSetting $settings): EtimsSetting
    {
        if (blank($settings->default_item_classification_code)) {
            throw new RuntimeException('Set a default item classification code before syncing the default service item.');
        }

        $response = $this->registerItem('General Service', $settings->default_item_classification_code);

        $settings->forceFill([
            'default_item_code' => $response['injongecode'] ?? $response['sid'] ?? null,
        ])->save();

        return $settings->refresh();
    }

    /**
     * @return array<string, mixed>
     */
    private function registerItem(string $name, string $classificationCode): array
    {
        $company = $this->companies->current();

        $response = $this->http->postJson(config('etims.urls.add_items', 'update_items'), [
            'product_name' => $name,
            'unspec' => $classificationCode,
            // No fixed catalog price — zytech prices per quotation/invoice line,
            // not per catalog item. 0 is a valid placeholder the middleware accepts.
            'price' => 0,
            'tin' => $company?->kra_pin,
            'bhfId' => '00',
            'stockable' => 0,
            'opening_stock' => 0,
        ]);

        if (($response['status'] ?? null) !== '000') {
            $message = $response['message'] ?? 'Unknown eTIMS error';

            throw new RuntimeException("Failed to register \"{$name}\" with eTIMS: {$message}");
        }

        return $response;
    }
}
