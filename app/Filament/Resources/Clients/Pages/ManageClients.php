<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Domains\Client\Services\ClientService;
use App\Filament\Resources\Clients\ClientResource;
use App\Models\Client;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageClients extends ManageRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->after(
                fn (Client $record) => app(ClientService::class)->initializeRecord($record),
            ),
        ];
    }
}
