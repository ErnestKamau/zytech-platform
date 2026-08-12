<?php

namespace App\Filament\Resources\Certifications\Pages;

use App\Domains\Company\Services\CompanyService;
use App\Filament\Resources\Certifications\CertificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCertifications extends ManageRecords
{
    protected static string $resource = CertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(fn () => app(CompanyService::class)->forget()),
        ];
    }
}
