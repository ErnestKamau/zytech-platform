<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Filament\Resources\NotificationTemplates\NotificationTemplateResource;
use Filament\Support\Icons\Heroicon;

class CommunicationHub extends AdminHubPage
{
    protected static ?string $navigationLabel = 'Communication';

    protected static ?string $title = 'Communication';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?int $navigationSort = 11;

    protected function getHubSubheading(): ?string
    {
        return 'Email templates and announcements.';
    }

    public function getHubSections(): array
    {
        return [
            [
                'title' => 'Messaging',
                'cards' => [
                    $this->hubCard('Notification templates', NotificationTemplateResource::class, Heroicon::OutlinedEnvelope),
                    $this->hubCard('Announcements', AnnouncementResource::class, Heroicon::OutlinedMegaphone),
                ],
            ],
        ];
    }
}
