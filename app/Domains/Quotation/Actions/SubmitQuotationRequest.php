<?php

namespace App\Domains\Quotation\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Quotation\Repositories\LeadSourceRepository;
use App\Domains\Quotation\Services\QuotationRequestService;
use App\Models\QuotationRequest;
use Illuminate\Http\UploadedFile;

final class SubmitQuotationRequest extends BaseAction
{
    public function __construct(
        private readonly QuotationRequestService $requests,
        private readonly LeadSourceRepository $sources,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $serviceIds
     * @param  list<UploadedFile>  $attachments
     * @param  list<string>  $productIds
     */
    public function handle(mixed ...$arguments): QuotationRequest
    {
        /** @var array<string, mixed> $data */
        $data = $arguments[0];
        /** @var list<string> $serviceIds */
        $serviceIds = $arguments[1] ?? [];
        /** @var list<UploadedFile> $attachments */
        $attachments = $arguments[2] ?? [];
        /** @var list<string> $productIds */
        $productIds = $arguments[3] ?? [];

        $source = $this->sources->findBySlug('website');

        $request = $this->requests->submit([
            ...$data,
            'lead_source_id' => $source?->id,
        ], $serviceIds, $productIds);

        foreach ($attachments as $file) {
            $path = $file->store('quotation-requests/'.$request->id, 'local');

            $request->attachments()->create([
                'original_name' => $file->getClientOriginalName(),
                'stored_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
            ]);
        }

        return $request->refresh(['services', 'products', 'attachments']);
    }
}
