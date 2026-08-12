<?php

namespace App\Filament\Resources\SiteVisits\Pages;

use App\Domains\Quotation\Actions\ScheduleSiteVisit;
use App\Filament\Resources\SiteVisits\SiteVisitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSiteVisits extends ManageRecords
{
    protected static string $resource = SiteVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->using(function (array $data) {
                return app(ScheduleSiteVisit::class)->handle($data);
            }),
        ];
    }
}
