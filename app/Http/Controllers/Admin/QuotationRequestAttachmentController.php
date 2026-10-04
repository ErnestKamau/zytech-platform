<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuotationRequestAttachment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class QuotationRequestAttachmentController extends Controller
{
    public function __invoke(QuotationRequestAttachment $attachment): StreamedResponse
    {
        $request = $attachment->request;

        abort_if($request === null, 404);
        Gate::authorize('view', $request);
        abort_unless(Storage::disk('local')->exists($attachment->stored_path), 404);

        return Storage::disk('local')->download($attachment->stored_path, $attachment->original_name);
    }
}
