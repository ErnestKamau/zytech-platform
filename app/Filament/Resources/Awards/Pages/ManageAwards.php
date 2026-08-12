<?php

namespace App\Filament\Resources\Awards\Pages;

use App\Domains\Company\Services\CompanyService;
use App\Filament\Resources\Awards\AwardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAwards extends ManageRecords
{
    protected static string $resource = AwardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(fn () => app(CompanyService::class)->forget()),
        ];
    }
}
