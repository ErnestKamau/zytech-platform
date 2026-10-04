<?php

namespace App\Domains\Commerce\Services;

use App\Core\Services\BaseService;
use App\Domains\Company\Services\CompanyService;
use App\Models\ProformaInvoice;
use App\Models\ProformaInvoiceDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ProformaInvoicePDFService extends BaseService
{
    public function __construct(private readonly CompanyService $companies) {}

    public function render(ProformaInvoice $proforma): DomPdf
    {
        $proforma->loadMissing(['client', 'quotation', 'items']);

        return Pdf::loadView('pdf.proforma-invoice', [
            'proforma' => $proforma,
            'company' => $this->companies->current(),
        ])->setPaper('a4');
    }

    public function generate(ProformaInvoice $proforma): ProformaInvoiceDocument
    {
        $pdf = $this->render($proforma);

        $relativePath = 'proforma-invoices/'.$proforma->getKey().'/'.$proforma->reference_number.'.pdf';
        Storage::disk('local')->put($relativePath, $pdf->output());

        $absolute = Storage::disk('local')->path($relativePath);
        $verification = strtoupper(Str::random(8));

        $existing = $proforma->documents()->where('kind', 'pdf')->latest()->first();

        if ($existing instanceof ProformaInvoiceDocument) {
            $existing->update([
                'title' => $proforma->reference_number.' — PDF',
                'stored_path' => $relativePath,
                'mime_type' => 'application/pdf',
                'size_bytes' => is_file($absolute) ? filesize($absolute) : 0,
                'verification_code' => $verification,
            ]);

            return $existing->fresh();
        }

        return ProformaInvoiceDocument::query()->create([
            'proforma_invoice_id' => $proforma->id,
            'title' => $proforma->reference_number.' — PDF',
            'kind' => 'pdf',
            'stored_path' => $relativePath,
            'mime_type' => 'application/pdf',
            'size_bytes' => is_file($absolute) ? filesize($absolute) : 0,
            'verification_code' => $verification,
        ]);
    }

    public function ensure(ProformaInvoice $proforma): ProformaInvoiceDocument
    {
        $document = $proforma->documents()->where('kind', 'pdf')->latest()->first();

        if (
            $document instanceof ProformaInvoiceDocument
            && filled($document->stored_path)
            && Storage::disk('local')->exists($document->stored_path)
            && ! $this->isStale($proforma, $document)
        ) {
            return $document;
        }

        return $this->generate($proforma);
    }

    private function isStale(ProformaInvoice $proforma, ProformaInvoiceDocument $document): bool
    {
        $lastChange = collect([
            $proforma->updated_at,
            $proforma->items()->max('updated_at'),
        ])->filter()->map(fn ($value) => Carbon::parse($value))->max();

        return $lastChange !== null && $document->updated_at !== null && $lastChange->gt($document->updated_at);
    }
}
