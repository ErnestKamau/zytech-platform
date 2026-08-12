<?php

namespace App\Filament\Resources\CompanyStatistics\Pages;

use App\Domains\Company\Services\CompanyService;
use App\Filament\Resources\CompanyStatistics\CompanyStatisticResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCompanyStatistics extends ManageRecords
{
    protected static string $resource = CompanyStatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(fn () => app(CompanyService::class)->forget()),
        ];
    }
}
