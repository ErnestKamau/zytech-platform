<?php

namespace App\Filament\Resources\ClientDocuments\Pages;

use App\Domains\Client\Services\DocumentService;
use App\Filament\Resources\ClientDocuments\ClientDocumentResource;
use App\Models\Client;
use App\Models\ClientDocument;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageClientDocuments extends ManageRecords
{
    protected static string $resource = ClientDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->using(function (array $data): ClientDocument {
                /** @var Client $client */
                $client = Client::query()->findOrFail($data['client_id']);

                return app(DocumentService::class)->register($client, $data);
            }),
        ];
    }
}
