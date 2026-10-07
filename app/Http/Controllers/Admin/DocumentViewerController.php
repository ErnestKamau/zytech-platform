<?php

namespace App\Http\Controllers\Admin;

use App\Core\Filament\BusinessHistoryAction;
use App\Domains\Company\Services\CompanyService;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\ProformaInvoice;
use App\Models\Quotation;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class DocumentViewerController extends Controller
{
    public function __construct(private readonly CompanyService $companies) {}

    public function quotation(Quotation $quotation): View
    {
        Gate::authorize('view', $quotation);

        $quotation->loadMissing(['client', 'request', 'sections.items', 'items', 'preparer']);

        return view('admin.document-viewer', [
            'type' => 'quotation',
            'docTitle' => 'Quotation',
            'reference' => $quotation->reference_number,
            'statusLabel' => $quotation->status->label(),
            'sheetView' => 'components.documents.quotation-sheet',
            'sheetData' => ['quotation' => $quotation, 'company' => $this->companies->current()],
            'currency' => $quotation->currency ?: 'KES',
            'total' => $quotation->total_amount,
            'downloadUrl' => route('filament.admin.quotations.pdf-preview', ['quotation' => $quotation]),
            'activities' => BusinessHistoryAction::activitiesFor($quotation),
            'notifications' => BusinessHistoryAction::notificationsFor($quotation, ['quotation_id']),
        ]);
    }

    public function proforma(ProformaInvoice $proformaInvoice): View
    {
        Gate::authorize('view', $proformaInvoice);

        $proformaInvoice->loadMissing(['client', 'quotation.request', 'items']);

        return view('admin.document-viewer', [
            'type' => 'proforma',
            'docTitle' => 'Proforma invoice',
            'reference' => $proformaInvoice->reference_number,
            'statusLabel' => $proformaInvoice->status->label(),
            'sheetView' => 'components.documents.proforma-sheet',
            'sheetData' => ['proforma' => $proformaInvoice, 'company' => $this->companies->current()],
            'currency' => $proformaInvoice->currency ?: 'KES',
            'total' => $proformaInvoice->total_amount,
            'downloadUrl' => route('filament.admin.proforma-invoices.pdf-preview', ['proformaInvoice' => $proformaInvoice]),
            'activities' => BusinessHistoryAction::activitiesFor($proformaInvoice),
            'notifications' => BusinessHistoryAction::notificationsFor($proformaInvoice, ['proforma_invoice_id']),
        ]);
    }

    public function invoice(Invoice $invoice): View
    {
        Gate::authorize('view', $invoice);

        $invoice->loadMissing(['client', 'quotation.request', 'salesOrder', 'items', 'payments']);

        return view('admin.document-viewer', [
            'type' => 'invoice',
            'docTitle' => 'Tax invoice',
            'reference' => $invoice->reference_number,
            'statusLabel' => $invoice->status->label(),
            'sheetView' => 'components.documents.invoice-sheet',
            'sheetData' => ['invoice' => $invoice, 'company' => $this->companies->current()],
            'currency' => $invoice->currency ?: 'KES',
            'total' => $invoice->total_amount,
            'downloadUrl' => route('filament.admin.invoices.pdf-preview', ['invoice' => $invoice]),
            'activities' => BusinessHistoryAction::activitiesFor($invoice),
            'notifications' => BusinessHistoryAction::notificationsFor($invoice, ['invoice_id']),
        ]);
    }
}
