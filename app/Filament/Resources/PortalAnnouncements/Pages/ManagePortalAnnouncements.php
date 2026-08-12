<?php

namespace App\Filament\Resources\PortalAnnouncements\Pages;

use App\Filament\Resources\PortalAnnouncements\PortalAnnouncementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePortalAnnouncements extends ManageRecords
{
    protected static string $resource = PortalAnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
