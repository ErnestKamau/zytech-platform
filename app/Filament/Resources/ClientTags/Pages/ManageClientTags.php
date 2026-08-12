<?php

namespace App\Filament\Resources\ClientTags\Pages;

use App\Filament\Resources\ClientTags\ClientTagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageClientTags extends ManageRecords
{
    protected static string $resource = ClientTagResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
