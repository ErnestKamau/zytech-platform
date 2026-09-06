<?php

namespace App\Domains\Commerce\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Commerce\Services\PurchaseOrderService;
use App\Models\Client;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use Illuminate\Http\UploadedFile;

final class UploadPurchaseOrder extends BaseAction
{
    public function __construct(private readonly PurchaseOrderService $purchaseOrders) {}

    public function handle(mixed ...$arguments): PurchaseOrder
    {
        /** @var Quotation $quotation */
        $quotation = $arguments[0];
        /** @var Client $client */
        $client = $arguments[1];
        /** @var string $poNumber */
        $poNumber = $arguments[2];
        /** @var UploadedFile $file */
        $file = $arguments[3];
        /** @var string|null $notes */
        $notes = $arguments[4] ?? null;

        return $this->purchaseOrders->upload($quotation, $client, $poNumber, $file, $notes);
    }
}
