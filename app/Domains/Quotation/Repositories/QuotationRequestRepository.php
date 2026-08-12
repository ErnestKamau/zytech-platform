<?php

namespace App\Domains\Quotation\Repositories;

use App\Models\QuotationRequest;
use Illuminate\Support\Collection;

final class QuotationRequestRepository
{
    public function findByReference(string $reference): ?QuotationRequest
    {
        return QuotationRequest::query()
            ->with(['services', 'source', 'statusHistory'])
            ->where('reference_number', $reference)
            ->first();
    }

    /**
     * @return Collection<int, QuotationRequest>
     */
    public function recent(int $limit = 20): Collection
    {
        return QuotationRequest::query()
            ->with(['services', 'source'])
            ->orderByDesc('submitted_at')
            ->limit($limit)
            ->get();
    }
}
