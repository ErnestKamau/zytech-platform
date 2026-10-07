<?php

namespace App\Domains\Commerce\Services;

use App\Core\Services\BaseService;
use App\Domains\Company\Services\CompanyService;
use App\Models\Invoice;
use App\Models\InvoiceDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class InvoicePDFService extends BaseService
{
    public function __construct(private readonly CompanyService $companies) {}

    public function render(Invoice $invoice): DomPdf
    {
        $invoice->loadMissing(['client', 'quotation', 'salesOrder', 'items', 'payments']);

        return Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'company' => $this->companies->current(),
        ])->setPaper('a4');
    }

    public function generate(Invoice $invoice): InvoiceDocument
    {
        $pdf = $this->render($invoice);

        $relativePath = 'invoices/'.$invoice->getKey().'/'.$invoice->reference_number.'.pdf';
        Storage::disk('local')->put($relativePath, $pdf->output());

        $absolute = Storage::disk('local')->path($relativePath);
        $verification = strtoupper(Str::random(8));

        $existing = $invoice->documents()->where('kind', 'pdf')->latest()->first();

        if ($existing instanceof InvoiceDocument) {
            $existing->update([
                'title' => $invoice->reference_number.' — PDF',
                'stored_path' => $relativePath,
                'mime_type' => 'application/pdf',
                'size_bytes' => is_file($absolute) ? filesize($absolute) : 0,
                'verification_code' => $verification,
            ]);

            return $existing->fresh();
        }

        return InvoiceDocument::query()->create([
            'invoice_id' => $invoice->id,
            'title' => $invoice->reference_number.' — PDF',
            'kind' => 'pdf',
            'stored_path' => $relativePath,
            'mime_type' => 'application/pdf',
            'size_bytes' => is_file($absolute) ? filesize($absolute) : 0,
            'verification_code' => $verification,
        ]);
    }

    public function ensure(Invoice $invoice): InvoiceDocument
    {
        $document = $invoice->documents()->where('kind', 'pdf')->latest()->first();

        if (
            $document instanceof InvoiceDocument
            && filled($document->stored_path)
            && Storage::disk('local')->exists($document->stored_path)
            && ! $this->isStale($invoice, $document)
        ) {
            return $document;
        }

        return $this->generate($invoice);
    }

    private function isStale(Invoice $invoice, InvoiceDocument $document): bool
    {
        $lastChange = collect([
            $invoice->updated_at,
            $invoice->items()->max('updated_at'),
        ])->filter()->map(fn ($value) => Carbon::parse($value))->max();

        return $lastChange !== null && $document->updated_at !== null && $lastChange->gt($document->updated_at);
    }
}
