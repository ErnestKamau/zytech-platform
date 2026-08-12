<?php

namespace App\Filament\Resources\MeetingRequests\Pages;

use App\Filament\Resources\MeetingRequests\MeetingRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMeetingRequests extends ManageRecords
{
    protected static string $resource = MeetingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
