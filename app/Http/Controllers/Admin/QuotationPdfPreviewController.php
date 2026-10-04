<?php

namespace App\Http\Controllers\Admin;

use App\Domains\Quotation\Services\QuotationPDFService;
use App\Http\Controllers\Controller;
use App\Models\Quotation;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class QuotationPdfPreviewController extends Controller
{
    public function __invoke(Quotation $quotation, QuotationPDFService $pdf): Response
    {
        Gate::authorize('view', $quotation);

        return $pdf->render($quotation)->stream($quotation->reference_number.'.pdf');
    }
}
