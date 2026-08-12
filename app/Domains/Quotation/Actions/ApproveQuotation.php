<?php

namespace App\Domains\Quotation\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Quotation;

final class ApproveQuotation extends BaseAction
{
    public function __construct(private readonly QuotationService $quotations) {}

    public function handle(mixed ...$arguments): Quotation
    {
        /** @var Quotation $quotation */
        $quotation = $arguments[0];
        $notes = isset($arguments[1]) ? (string) $arguments[1] : null;

        return $this->quotations->approve($quotation, $notes);
    }
}
