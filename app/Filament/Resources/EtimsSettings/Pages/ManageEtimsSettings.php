<?php

namespace App\Filament\Resources\EtimsSettings\Pages;

use App\Filament\Resources\EtimsSettings\EtimsSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEtimsSettings extends ManageRecords
{
    protected static string $resource = EtimsSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
