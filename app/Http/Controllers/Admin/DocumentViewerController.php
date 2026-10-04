<?php

namespace App\Http\Controllers\Admin;

use App\Core\Filament\BusinessHistoryAction;
use App\Http\Controllers\Controller;
use App\Models\ProformaInvoice;
use App\Models\Quotation;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class DocumentViewerController extends Controller
{
    public function quotation(Quotation $quotation): View
    {
        Gate::authorize('view', $quotation);

        $quotation->loadMissing(['client']);

        return view('admin.document-viewer', [
            'type' => 'quotation',
            'docTitle' => 'Quotation',
            'reference' => $quotation->reference_number,
            'statusLabel' => $quotation->status->label(),
            'issuedDate' => $quotation->sent_at ?? $quotation->created_at,
            'validUntilLabel' => 'Valid until',
            'validUntil' => $quotation->valid_until,
            'clientName' => $quotation->client?->name,
            'clientEmail' => $quotation->client?->email,
            'currency' => $quotation->currency ?: 'KES',
            'subtotal' => $quotation->subtotal,
            'discount' => $quotation->discount_amount,
            'tax' => $quotation->tax_amount,
            'total' => $quotation->total_amount,
            'paymentTerms' => $quotation->terms,
            'streamUrl' => route('filament.admin.quotations.pdf-preview', ['quotation' => $quotation]),
            'downloadUrl' => route('filament.admin.quotations.pdf-preview', ['quotation' => $quotation]),
            'activities' => BusinessHistoryAction::activitiesFor($quotation),
            'notifications' => BusinessHistoryAction::notificationsFor($quotation, ['quotation_id']),
            'payments' => collect(),
        ]);
    }

    public function proforma(ProformaInvoice $proformaInvoice): View
    {
        Gate::authorize('view', $proformaInvoice);

        $proformaInvoice->loadMissing(['client', 'quotation']);

        return view('admin.document-viewer', [
            'type' => 'proforma',
            'docTitle' => 'Proforma invoice',
            'reference' => $proformaInvoice->reference_number,
            'statusLabel' => $proformaInvoice->status->label(),
            'issuedDate' => $proformaInvoice->issued_at ?? $proformaInvoice->created_at,
            'validUntilLabel' => 'Valid until',
            'validUntil' => $proformaInvoice->valid_until,
            'clientName' => $proformaInvoice->client?->name,
            'clientEmail' => $proformaInvoice->client?->email,
            'currency' => $proformaInvoice->currency ?: 'KES',
            'subtotal' => $proformaInvoice->subtotal,
            'discount' => $proformaInvoice->discount_amount,
            'tax' => $proformaInvoice->tax_amount,
            'total' => $proformaInvoice->total_amount,
            'paymentTerms' => $proformaInvoice->payment_terms,
            'streamUrl' => route('filament.admin.proforma-invoices.pdf-preview', ['proformaInvoice' => $proformaInvoice]),
            'downloadUrl' => route('filament.admin.proforma-invoices.pdf-preview', ['proformaInvoice' => $proformaInvoice]),
            'activities' => BusinessHistoryAction::activitiesFor($proformaInvoice),
            'notifications' => BusinessHistoryAction::notificationsFor($proformaInvoice, ['proforma_invoice_id']),
            'payments' => collect(),
        ]);
    }
}
