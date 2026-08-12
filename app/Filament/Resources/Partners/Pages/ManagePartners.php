<?php

namespace App\Filament\Resources\Partners\Pages;

use App\Domains\Company\Services\PartnerService;
use App\Filament\Resources\Partners\PartnerResource;
use App\Models\Partner;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePartners extends ManageRecords
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(fn (array $data): Partner => app(PartnerService::class)->add($data)),
        ];
    }
}
