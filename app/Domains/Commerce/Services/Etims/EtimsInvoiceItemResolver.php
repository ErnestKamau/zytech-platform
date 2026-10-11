<?php

namespace App\Domains\Commerce\Services\Etims;

use App\Models\EtimsSetting;
use App\Models\InvoiceItem;
use RuntimeException;

/**
 * Resolves the KRA itemCd/itemClsCd for an invoice line. Zytech invoice
 * lines are free-text by default (no catalog linkage) — a line only gets a
 * real catalog item code when it's tied to a synced Product; everything
 * else falls back to the one pre-registered "default service" item
 * configured in eTIMS Settings.
 */
final class EtimsInvoiceItemResolver
{
    /**
     * @return array{itemCd: string, itemClsCd: string}
     */
    public function resolve(InvoiceItem $item, EtimsSetting $settings): array
    {
        $product = $item->product;

        if ($product !== null && filled($product->kra_item_code) && filled($product->kra_classification_code)) {
            return [
                'itemCd' => $product->kra_item_code,
                'itemClsCd' => $product->kra_classification_code,
            ];
        }

        if (filled($settings->default_item_code) && filled($settings->default_item_classification_code)) {
            return [
                'itemCd' => $settings->default_item_code,
                'itemClsCd' => $settings->default_item_classification_code,
            ];
        }

        throw new RuntimeException(
            "No KRA item code for line \"{$item->label}\" and no default service item configured in eTIMS Settings."
        );
    }
}
