<?php

namespace App\Domains\Quotation\Support;

use App\Models\ProformaInvoice;
use App\Models\Quotation;
use App\Models\QuotationRequest;
use Illuminate\Support\Str;

final class ReferenceNumber
{
    public static function forRequest(): string
    {
        return self::generate('ZQR');
    }

    public static function forQuotation(): string
    {
        return self::generate('ZQ');
    }

    public static function forProforma(): string
    {
        $date = now()->format('Ymd');

        do {
            $candidate = sprintf('%s-%s-%s', 'ZPI', $date, strtoupper(Str::random(4)));
        } while (ProformaInvoice::query()->where('reference_number', $candidate)->exists());

        return $candidate;
    }

    private static function generate(string $prefix): string
    {
        $date = now()->format('Ymd');

        do {
            $candidate = sprintf('%s-%s-%s', $prefix, $date, strtoupper(Str::random(4)));
        } while (
            QuotationRequest::query()->where('reference_number', $candidate)->exists()
            || Quotation::query()->where('reference_number', $candidate)->exists()
        );

        return $candidate;
    }
}
