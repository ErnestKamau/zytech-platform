<?php

namespace App\Http\Controllers\Portal;

use App\Domains\Portal\Actions\DownloadDocument;
use App\Domains\Portal\Repositories\PortalRepository;
use App\Domains\Quotation\Services\QuotationPDFService;
use App\Http\Controllers\Controller;
use App\Models\ClientDocument;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class PortalFileController extends Controller
{
    public function streamQuotationPdf(
        Request $request,
        Quotation $quotation,
        PortalRepository $portal,
        QuotationPDFService $pdf,
    ): StreamedResponse {
        $this->assertOwnsQuotation($request, $portal, $quotation);
        $document = $pdf->ensure($quotation);

        return Storage::disk('local')->response(
            $document->stored_path,
            basename((string) $document->stored_path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$quotation->reference_number.'.pdf"',
            ],
        );
    }

    public function downloadQuotationPdf(
        Request $request,
        Quotation $quotation,
        PortalRepository $portal,
        QuotationPDFService $pdf,
    ): StreamedResponse {
        $this->assertOwnsQuotation($request, $portal, $quotation);
        $document = $pdf->ensure($quotation);

        return Storage::disk('local')->download(
            $document->stored_path,
            $quotation->reference_number.'.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function downloadDocument(
        Request $request,
        ClientDocument $document,
        PortalRepository $portal,
        DownloadDocument $action,
    ): StreamedResponse {
        $user = $request->user();
        abort_unless($user !== null, 403);

        $client = $portal->clientForUser($user) ?? abort(403);
        abort_unless($document->client_id === $client->id, 403);

        $action->handle($client, $user, $document);

        abort_unless(filled($document->stored_path) && Storage::disk('local')->exists($document->stored_path), 404);

        return Storage::disk('local')->download(
            $document->stored_path,
            $document->title,
            array_filter(['Content-Type' => $document->mime_type]),
        );
    }

    private function assertOwnsQuotation(Request $request, PortalRepository $portal, Quotation $quotation): void
    {
        $user = $request->user();
        abort_unless($user !== null, 403);
        $client = $portal->clientForUser($user) ?? abort(403);
        abort_unless($quotation->client_id === $client->id, 403);
    }
}
