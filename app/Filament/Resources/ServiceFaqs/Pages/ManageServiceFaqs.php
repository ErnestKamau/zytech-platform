<?php

namespace App\Filament\Resources\ServiceFaqs\Pages;

use App\Domains\Service\Services\ServiceService;
use App\Filament\Resources\ServiceFaqs\ServiceFaqResource;
use App\Models\ServiceFaq;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageServiceFaqs extends ManageRecords
{
    protected static string $resource = ServiceFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(fn (ServiceFaq $record) => app(ServiceService::class)->forget($record->service?->slug)),
        ];
    }
}
