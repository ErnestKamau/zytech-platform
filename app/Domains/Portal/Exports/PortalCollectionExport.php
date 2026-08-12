<?php

namespace App\Domains\Portal\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

final class PortalCollectionExport implements FromCollection, WithHeadings
{
    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @param  list<string>  $headings
     */
    public function __construct(
        private readonly Collection $rows,
        private readonly array $headings,
    ) {}

    public function collection(): Collection
    {
        return $this->rows;
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return $this->headings;
    }
}
