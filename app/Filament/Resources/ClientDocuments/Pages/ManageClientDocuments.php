<?php

namespace App\Filament\Resources\ClientDocuments\Pages;

use App\Domains\Client\Services\DocumentService;
use App\Filament\Imports\ClientDocumentImporter;
use App\Filament\Resources\ClientDocuments\ClientDocumentResource;
use App\Models\Client;
use App\Models\ClientDocument;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Storage;

class ManageClientDocuments extends ManageRecords
{
    protected static string $resource = ClientDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(ClientDocumentImporter::class),
            CreateAction::make()->using(function (array $data): ClientDocument {
                /** @var Client $client */
                $client = Client::query()->findOrFail($data['client_id']);

                if (is_array($data['stored_path'] ?? null)) {
                    $data['stored_path'] = $data['stored_path'][0] ?? null;
                }

                if (! empty($data['stored_path']) && Storage::disk('local')->exists($data['stored_path'])) {
                    $data['mime_type'] = Storage::disk('local')->mimeType($data['stored_path']) ?: ($data['mime_type'] ?? null);
                    $data['size_bytes'] = Storage::disk('local')->size($data['stored_path']) ?: 0;
                }

                return app(DocumentService::class)->register($client, $data);
            }),
        ];
    }
}
