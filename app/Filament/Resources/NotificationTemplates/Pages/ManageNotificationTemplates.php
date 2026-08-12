<?php

namespace App\Filament\Resources\NotificationTemplates\Pages;

use App\Domains\Communication\Services\TemplateService;
use App\Filament\Resources\NotificationTemplates\NotificationTemplateResource;
use App\Models\NotificationTemplate;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageNotificationTemplates extends ManageRecords
{
    protected static string $resource = NotificationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->after(fn (NotificationTemplate $record) => app(TemplateService::class)->forget()),
        ];
    }
}
