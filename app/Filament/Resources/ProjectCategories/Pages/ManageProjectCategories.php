<?php

namespace App\Filament\Resources\ProjectCategories\Pages;

use App\Domains\Project\Services\ProjectService;
use App\Filament\Resources\ProjectCategories\ProjectCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProjectCategories extends ManageRecords
{
    protected static string $resource = ProjectCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->after(fn () => app(ProjectService::class)->forget()),
        ];
    }
}
