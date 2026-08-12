<?php

namespace App\Domains\Quotation\Services;

use App\Core\Services\BaseService;
use App\Models\Quotation;
use App\Models\QuotationDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class QuotationPDFService extends BaseService
{
    public function generate(Quotation $quotation): QuotationDocument
    {
        $quotation->loadMissing(['client', 'sections.items', 'items', 'preparer']);

        $pdf = Pdf::loadView('pdf.quotation', [
            'quotation' => $quotation,
        ])->setPaper('a4');

        $relativePath = 'quotations/'.$quotation->getKey().'/'.$quotation->reference_number.'.pdf';
        Storage::disk('local')->put($relativePath, $pdf->output());

        $absolute = Storage::disk('local')->path($relativePath);
        $verification = strtoupper(Str::random(8));

        $existing = $quotation->documents()->where('kind', 'pdf')->latest()->first();

        if ($existing instanceof QuotationDocument) {
            $existing->update([
                'title' => $quotation->reference_number.' — PDF',
                'stored_path' => $relativePath,
                'mime_type' => 'application/pdf',
                'size_bytes' => is_file($absolute) ? filesize($absolute) : 0,
                'verification_code' => $verification,
            ]);

            return $existing->fresh();
        }

        return QuotationDocument::query()->create([
            'quotation_id' => $quotation->id,
            'title' => $quotation->reference_number.' — PDF',
            'kind' => 'pdf',
            'stored_path' => $relativePath,
            'mime_type' => 'application/pdf',
            'size_bytes' => is_file($absolute) ? filesize($absolute) : 0,
            'verification_code' => $verification,
        ]);
    }

    public function ensure(Quotation $quotation): QuotationDocument
    {
        $document = $quotation->documents()->where('kind', 'pdf')->latest()->first();

        if ($document instanceof QuotationDocument && filled($document->stored_path) && Storage::disk('local')->exists($document->stored_path)) {
            return $document;
        }

        return $this->generate($quotation);
    }
}
