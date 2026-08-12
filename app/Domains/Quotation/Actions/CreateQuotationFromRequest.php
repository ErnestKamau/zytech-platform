<?php

namespace App\Domains\Quotation\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Quotation;
use App\Models\QuotationRequest;

final class CreateQuotationFromRequest extends BaseAction
{
    public function __construct(private readonly QuotationService $quotations) {}

    public function handle(mixed ...$arguments): Quotation
    {
        /** @var QuotationRequest $request */
        $request = $arguments[0];

        return $this->quotations->createFromRequest($request);
    }
}
