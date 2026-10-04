<?php

namespace App\Http\Controllers\Admin;

use App\Domains\Commerce\Services\ProformaInvoicePDFService;
use App\Http\Controllers\Controller;
use App\Models\ProformaInvoice;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class ProformaInvoicePdfPreviewController extends Controller
{
    public function __invoke(ProformaInvoice $proformaInvoice, ProformaInvoicePDFService $pdf): Response
    {
        Gate::authorize('view', $proformaInvoice);

        return $pdf->render($proformaInvoice)->stream($proformaInvoice->reference_number.'.pdf');
    }
}
