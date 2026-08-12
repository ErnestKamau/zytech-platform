<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Domains\Company\Services\CompanyService;
use App\Filament\Resources\Faqs\FaqResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageFaqs extends ManageRecords
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(fn () => app(CompanyService::class)->forget()),
        ];
    }
}
