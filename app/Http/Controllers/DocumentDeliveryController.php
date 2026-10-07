<?php

namespace App\Http\Controllers;

use App\Domains\Commerce\Services\InvoicePDFService;
use App\Domains\Commerce\Services\ProformaInvoicePDFService;
use App\Domains\Quotation\Services\QuotationPDFService;
use App\Models\Invoice;
use App\Models\ProformaInvoice;
use App\Models\Quotation;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Unauthenticated-but-signed PDF fetch for machine consumers (Twilio WhatsApp media).
 * Security relies solely on the HMAC signature + expiry from URL::temporarySignedRoute();
 * never log the full signed URL, only the route name + record id.
 */
final class DocumentDeliveryController extends Controller
{
    public function quotation(Quotation $quotation, QuotationPDFService $pdf): StreamedResponse
    {
        abort_unless($quotation->isSharedWithClient(), 404);

        $document = $pdf->ensure($quotation);

        return Storage::disk('local')->response(
            $document->stored_path,
            $quotation->reference_number.'.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function proforma(ProformaInvoice $proformaInvoice, ProformaInvoicePDFService $pdf): StreamedResponse
    {
        $document = $pdf->ensure($proformaInvoice);

        return Storage::disk('local')->response(
            $document->stored_path,
            $proformaInvoice->reference_number.'.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function invoice(Invoice $invoice, InvoicePDFService $pdf): StreamedResponse
    {
        $document = $pdf->ensure($invoice);

        return Storage::disk('local')->response(
            $document->stored_path,
            $invoice->reference_number.'.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }
}
