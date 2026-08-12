<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Domains\Company\Actions\CreateCompanyProfile;
use App\Filament\Resources\Companies\CompanyResource;
use App\Models\Company;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCompanies extends ManageRecords
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(fn (array $data): Company => app(CreateCompanyProfile::class)->handle($data)),
        ];
    }
}
