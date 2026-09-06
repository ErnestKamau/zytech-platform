<?php

namespace App\Domains\Commerce\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Commerce\Services\SalesOrderService;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\DB;

final class CreateSalesOrderAndDraftInvoice extends BaseAction
{
    public function __construct(private readonly SalesOrderService $orders) {}

    /**
     * @return array{sales_order: SalesOrder, invoice: Invoice}
     */
    public function handle(mixed ...$arguments): array
    {
        /** @var Quotation $quotation */
        $quotation = $arguments[0];
        /** @var PurchaseOrder|null $purchaseOrder */
        $purchaseOrder = $arguments[1] ?? null;

        return DB::transaction(function () use ($quotation, $purchaseOrder): array {
            $order = $this->orders->createFromAcceptedQuote($quotation, $purchaseOrder);
            $invoice = $this->orders->createDraftInvoice($order);

            return [
                'sales_order' => $order,
                'invoice' => $invoice,
            ];
        });
    }
}
