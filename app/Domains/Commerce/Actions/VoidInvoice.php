<?php

namespace App\Domains\Commerce\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Enums\InvoiceStatus;
use App\Domains\Operations\Services\ActivityLogger;
use App\Models\Invoice;
use RuntimeException;

final class VoidInvoice extends BaseAction
{
    public function __construct(private readonly ActivityLogger $activities) {}

    public function handle(mixed ...$arguments): Invoice
    {
        /** @var Invoice $invoice */
        $invoice = $arguments[0];
        /** @var string $reason */
        $reason = $arguments[1];

        if (! $invoice->isVoidable()) {
            throw new RuntimeException('This invoice cannot be voided — it either has payments recorded or has already moved past Draft/Issued. Issue a credit note instead.');
        }

        $invoice->forceFill([
            'status' => InvoiceStatus::Void,
            'voided_at' => now(),
            'void_reason' => $reason,
        ])->save();

        $this->activities->log($invoice, 'invoice.voided', ['reason' => $reason]);

        return $invoice->refresh();
    }
}
