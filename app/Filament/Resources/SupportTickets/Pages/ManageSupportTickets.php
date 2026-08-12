<?php

namespace App\Filament\Resources\SupportTickets\Pages;

use App\Domains\Portal\Support\TicketReference;
use App\Filament\Resources\SupportTickets\SupportTicketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSupportTickets extends ManageRecords
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                $data['reference_number'] ??= TicketReference::next();

                return $data;
            }),
        ];
    }
}
