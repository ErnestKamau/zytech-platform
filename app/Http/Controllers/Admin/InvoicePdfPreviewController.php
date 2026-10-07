<?php

namespace App\Http\Controllers\Admin;

use App\Domains\Commerce\Services\InvoicePDFService;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class InvoicePdfPreviewController extends Controller
{
    public function __invoke(Invoice $invoice, InvoicePDFService $pdf): Response
    {
        Gate::authorize('view', $invoice);

        return $pdf->render($invoice)->stream($invoice->reference_number.'.pdf');
    }
}
