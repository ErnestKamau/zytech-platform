<?php

namespace App\Filament\Resources\Services\Pages;

use App\Domains\Service\Services\ServiceService;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageServices extends ManageRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(fn (Service $record) => app(ServiceService::class)->persisted($record, created: true)),
        ];
    }
}
