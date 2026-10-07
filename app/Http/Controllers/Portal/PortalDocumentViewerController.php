<?php

namespace App\Http\Controllers\Portal;

use App\Core\Enums\InvoiceStatus;
use App\Domains\Portal\Repositories\PortalRepository;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PortalDocumentViewerController extends Controller
{
    public function invoice(Request $request, Invoice $invoice, PortalRepository $portal): View
    {
        $user = $request->user();
        abort_unless($user !== null, 403);
        $client = $portal->clientForUser($user) ?? abort(403);
        abort_unless($invoice->client_id === $client->id, 403);
        abort_unless($invoice->status !== InvoiceStatus::Draft, 404);

        $invoice->loadMissing(['items', 'payments']);

        return view('portal.document-viewer', [
            'docTitle' => 'Tax invoice',
            'reference' => $invoice->reference_number,
            'statusLabel' => $invoice->status->label(),
            'currency' => $invoice->currency ?: 'KES',
            'total' => $invoice->total_amount,
            'streamUrl' => route('portal.invoices.pdf', ['invoice' => $invoice]),
            'downloadUrl' => route('portal.invoices.pdf.download', ['invoice' => $invoice]),
            'backUrl' => route('portal.invoices'),
            'backLabel' => 'Back to invoices',
        ]);
    }
}
