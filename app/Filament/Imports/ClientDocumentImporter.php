<?php

namespace App\Filament\Imports;

use App\Core\Enums\DocumentVisibility;
use App\Domains\Client\Services\DocumentService;
use App\Models\Client;
use App\Models\ClientDocument;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Validation\ValidationException;

class ClientDocumentImporter extends Importer
{
    protected static ?string $model = ClientDocument::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('client_email')
                ->label('Client email')
                ->requiredMapping()
                ->rules(['required', 'email']),
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('kind')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('visibility')
                ->rules(['nullable', 'in:staff,client,private']),
            ImportColumn::make('stored_path')
                ->label('Storage path')
                ->rules(['nullable', 'max:500']),
            ImportColumn::make('mime_type')
                ->rules(['nullable', 'max:120']),
            ImportColumn::make('size_bytes')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0']),
        ];
    }

    public function resolveRecord(): ClientDocument
    {
        $email = (string) ($this->data['client_email'] ?? '');
        $client = Client::query()->where('email', $email)->first();

        if ($client === null) {
            throw ValidationException::withMessages([
                'client_email' => "No client found for email [{$email}].",
            ]);
        }

        $visibility = DocumentVisibility::tryFrom((string) ($this->data['visibility'] ?? DocumentVisibility::Client->value))
            ?? DocumentVisibility::Client;

        return app(DocumentService::class)->register($client, [
            'title' => (string) ($this->data['title'] ?? 'Imported document'),
            'kind' => (string) ($this->data['kind'] ?? 'general'),
            'visibility' => $visibility,
            'stored_path' => $this->data['stored_path'] ?? null,
            'mime_type' => $this->data['mime_type'] ?? null,
            'size_bytes' => (int) ($this->data['size_bytes'] ?? 0),
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your client document import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
