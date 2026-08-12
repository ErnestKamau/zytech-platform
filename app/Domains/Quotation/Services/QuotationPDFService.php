<?php

namespace App\Domains\Quotation\Services;

use App\Core\Services\BaseService;
use App\Models\Quotation;
use App\Models\QuotationDocument;
use Illuminate\Support\Str;

final class QuotationPDFService extends BaseService
{
    public function generate(Quotation $quotation): QuotationDocument
    {
        $verification = strtoupper(Str::random(8));

        return QuotationDocument::query()->create([
            'quotation_id' => $quotation->id,
            'title' => $quotation->reference_number.' — PDF',
            'kind' => 'pdf',
            'stored_path' => null,
            'mime_type' => 'application/pdf',
            'verification_code' => $verification,
        ]);
    }
}
