<?php

namespace App\Domains\Commerce\Services;

use App\Core\Enums\PurchaseOrderStatus;
use App\Core\Services\BaseService;
use App\Domains\Commerce\Events\PurchaseOrderUploaded;
use App\Models\Client;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

final class PurchaseOrderService extends BaseService
{
    public function markNotRequired(Quotation $quotation, Client $client): PurchaseOrder
    {
        $existing = PurchaseOrder::query()
            ->where('quotation_id', $quotation->id)
            ->first();

        if ($existing !== null) {
            $existing->forceFill([
                'status' => PurchaseOrderStatus::NotRequired,
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
            ])->save();

            return $existing->refresh();
        }

        return PurchaseOrder::query()->create([
            'reference_number' => $this->nextReference(),
            'client_id' => $client->id,
            'quotation_id' => $quotation->id,
            'status' => PurchaseOrderStatus::NotRequired,
            'currency' => $quotation->currency ?: 'KES',
            'total_amount' => $quotation->total_amount,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
    }

    public function upload(
        Quotation $quotation,
        Client $client,
        string $poNumber,
        UploadedFile $file,
        ?string $notes = null,
    ): PurchaseOrder {
        $path = $file->store('purchase-orders/'.$quotation->id, 'local');

        $po = PurchaseOrder::query()
            ->where('quotation_id', $quotation->id)
            ->first();

        $attributes = [
            'po_number' => $poNumber,
            'client_id' => $client->id,
            'quotation_id' => $quotation->id,
            'status' => PurchaseOrderStatus::Uploaded,
            'currency' => $quotation->currency ?: 'KES',
            'total_amount' => $quotation->total_amount,
            'document_path' => $path,
            'document_original_name' => $file->getClientOriginalName(),
            'notes' => $notes,
            'uploaded_by' => auth()->id(),
            'uploaded_at' => now(),
        ];

        if ($po === null) {
            $po = PurchaseOrder::query()->create([
                ...$attributes,
                'reference_number' => $this->nextReference(),
            ]);
        } else {
            $po->forceFill($attributes)->save();
        }

        event(new PurchaseOrderUploaded($po->refresh()));

        return $po->refresh();
    }

    public function accept(PurchaseOrder $purchaseOrder, ?string $notes = null): PurchaseOrder
    {
        $purchaseOrder->forceFill([
            'status' => PurchaseOrderStatus::Accepted,
            'validation_notes' => $notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ])->save();

        return $purchaseOrder->refresh();
    }

    public function markMismatch(PurchaseOrder $purchaseOrder, string $notes): PurchaseOrder
    {
        $purchaseOrder->forceFill([
            'status' => PurchaseOrderStatus::Mismatch,
            'validation_notes' => $notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ])->save();

        return $purchaseOrder->refresh();
    }

    public function forQuotation(Quotation $quotation): ?PurchaseOrder
    {
        return PurchaseOrder::query()->where('quotation_id', $quotation->id)->first();
    }

    private function nextReference(): string
    {
        return sprintf('ZPO-%s-%s', now()->format('Ymd'), Str::upper(Str::random(5)));
    }
}
