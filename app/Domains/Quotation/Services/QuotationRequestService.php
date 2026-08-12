<?php

namespace App\Domains\Quotation\Services;

use App\Core\Enums\ClientTimelineEvent;
use App\Core\Enums\QuotationStatus;
use App\Core\Services\BaseService;
use App\Domains\Client\Services\ClientService;
use App\Domains\Client\Services\TimelineService;
use App\Domains\Quotation\Data\QuotationRequestData;
use App\Domains\Quotation\Events\QuotationRequestSubmitted;
use App\Domains\Quotation\Repositories\QuotationRequestRepository;
use App\Domains\Quotation\Support\ReferenceNumber;
use App\Models\QuotationRequest;
use App\Models\QuotationStatusHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class QuotationRequestService extends BaseService
{
    public function __construct(
        private readonly QuotationRequestRepository $requests,
        private readonly LeadService $leads,
        private readonly ClientService $clients,
        private readonly TimelineService $clientTimeline,
    ) {}

    public function findByReference(string $reference): ?QuotationRequestData
    {
        $request = $this->requests->findByReference($reference);

        return $request === null ? null : $this->toData($request);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  list<string>  $serviceIds
     */
    public function submit(array $payload, array $serviceIds = []): QuotationRequest
    {
        return DB::transaction(function () use ($payload, $serviceIds): QuotationRequest {
            $client = $this->clients->findOrCreateFromLead(
                (string) $payload['full_name'],
                (string) $payload['email'],
                isset($payload['phone']) ? (string) $payload['phone'] : null,
            );

            $lead = $this->leads->create([
                'client_id' => $client->id,
                'lead_source_id' => $payload['lead_source_id'] ?? null,
                'full_name' => $payload['full_name'],
                'email' => $payload['email'],
                'phone' => $payload['phone'] ?? null,
            ]);

            $request = QuotationRequest::query()->create([
                ...$payload,
                'reference_number' => ReferenceNumber::forRequest(),
                'sales_lead_id' => $lead->id,
                'client_id' => $client->id,
                'status' => QuotationStatus::Pending,
                'submitted_at' => now(),
            ]);

            $this->clientTimeline->record(
                $client,
                ClientTimelineEvent::QuotationRequested,
                'Quotation requested',
                'Reference '.$request->reference_number,
                ['quotation_request_id' => $request->id],
            );

            if ($serviceIds !== []) {
                $request->services()->sync($serviceIds);
            }

            $this->recordStatus($request, null, QuotationStatus::Pending, 'Submitted from public website');

            event(new QuotationRequestSubmitted($request->fresh(['services', 'source'])));

            return $request->refresh();
        });
    }

    public function transition(QuotationRequest $request, QuotationStatus $status, ?string $notes = null): QuotationRequest
    {
        $from = $request->status;
        $request->forceFill(['status' => $status])->save();

        $this->recordStatus($request, $from, $status, $notes);

        return $request->refresh();
    }

    /**
     * @return Collection<int, QuotationRequest>
     */
    public function recent(): Collection
    {
        return $this->requests->recent();
    }

    private function recordStatus(
        QuotationRequest $request,
        ?QuotationStatus $from,
        QuotationStatus $to,
        ?string $notes = null,
    ): void {
        QuotationStatusHistory::query()->create([
            'quotation_request_id' => $request->id,
            'from_status' => $from?->value,
            'to_status' => $to->value,
            'notes' => $notes,
            'changed_by' => auth()->id(),
        ]);
    }

    private function toData(QuotationRequest $request): QuotationRequestData
    {
        return QuotationRequestData::fromArray([
            ...$request->toArray(),
            'service_names' => $request->services->pluck('title')->all(),
            'submitted_at' => $request->submitted_at?->toIso8601String(),
        ]);
    }
}
