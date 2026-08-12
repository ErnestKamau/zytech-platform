<?php

namespace App\Filament\Resources\ServiceCategories\Pages;

use App\Domains\Service\Services\ServiceService;
use App\Filament\Resources\ServiceCategories\ServiceCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageServiceCategories extends ManageRecords
{
    protected static string $resource = ServiceCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(fn () => app(ServiceService::class)->forget()),
        ];
    }
}
