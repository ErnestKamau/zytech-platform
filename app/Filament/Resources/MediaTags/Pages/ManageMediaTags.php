<?php

namespace App\Filament\Resources\MediaTags\Pages;

use App\Filament\Resources\MediaTags\MediaTagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMediaTags extends ManageRecords
{
    protected static string $resource = MediaTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
