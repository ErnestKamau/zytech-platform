<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Domains\Communication\Services\AnnouncementService;
use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Models\Announcement;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAnnouncements extends ManageRecords
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->after(fn (Announcement $record) => app(AnnouncementService::class)->forget()),
        ];
    }
}
