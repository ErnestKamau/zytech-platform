<?php

namespace App\Domains\Quotation\Services;

use App\Core\Services\BaseService;
use App\Domains\Company\Services\CompanyService;
use App\Models\Quotation;
use App\Models\QuotationDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class QuotationPDFService extends BaseService
{
    public function __construct(private readonly CompanyService $companies) {}

    public function render(Quotation $quotation): DomPdf
    {
        $quotation->loadMissing(['client', 'request', 'sections.items', 'items', 'preparer']);

        return Pdf::loadView('pdf.quotation', [
            'quotation' => $quotation,
            'company' => $this->companies->current(),
        ])->setPaper('a4');
    }

    public function generate(Quotation $quotation): QuotationDocument
    {
        $pdf = $this->render($quotation);

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

        if (
            $document instanceof QuotationDocument
            && filled($document->stored_path)
            && Storage::disk('local')->exists($document->stored_path)
            && ! $this->isStale($quotation, $document)
        ) {
            return $document;
        }

        return $this->generate($quotation);
    }

    private function isStale(Quotation $quotation, QuotationDocument $document): bool
    {
        $lastChange = collect([
            $quotation->updated_at,
            $quotation->items()->max('updated_at'),
        ])->filter()->map(fn ($value) => Carbon::parse($value))->max();

        return $lastChange !== null && $document->updated_at !== null && $lastChange->gt($document->updated_at);
    }
}
