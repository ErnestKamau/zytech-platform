<?php

namespace App\Domains\Quotation\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Quotation;

final class RequestQuoteRevision extends BaseAction
{
    public function __construct(private readonly QuotationService $quotations) {}

    public function handle(mixed ...$arguments): Quotation
    {
        /** @var Quotation $quotation */
        $quotation = $arguments[0];
        /** @var string $notes */
        $notes = (string) ($arguments[1] ?? '');

        return $this->quotations->requestRevision($quotation, $notes);
    }
}
