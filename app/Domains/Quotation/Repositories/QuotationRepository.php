<?php

namespace App\Domains\Quotation\Repositories;

use App\Models\Quotation;
use Illuminate\Support\Collection;

final class QuotationRepository
{
    public function findByReference(string $reference): ?Quotation
    {
        return Quotation::query()
            ->with(['sections.items', 'request', 'documents', 'approvals'])
            ->where('reference_number', $reference)
            ->first();
    }

    /**
     * @return Collection<int, Quotation>
     */
    public function pipeline(): Collection
    {
        return Quotation::query()
            ->with('request')
            ->whereNotIn('status', ['accepted', 'rejected', 'completed', 'expired'])
            ->orderByDesc('updated_at')
            ->get();
    }
}
