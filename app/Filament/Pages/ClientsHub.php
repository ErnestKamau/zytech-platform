<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ClientDocuments\ClientDocumentResource;
use App\Filament\Resources\ClientGroups\ClientGroupResource;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\ClientTags\ClientTagResource;
use App\Filament\Resources\DomainActivities\DomainActivityResource;
use App\Filament\Resources\FollowUps\FollowUpResource;
use App\Filament\Resources\MeetingRequests\MeetingRequestResource;
use App\Filament\Resources\PortalAnnouncements\PortalAnnouncementResource;
use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\Client;
use App\Models\SupportTicket;
use Filament\Support\Icons\Heroicon;

class ClientsHub extends AdminHubPage
{
    protected static ?string $navigationLabel = 'Clients';

    protected static ?string $title = 'Clients';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 5;

    protected function getHubSubheading(): ?string
    {
        return 'Client records, portal, support, and follow-ups.';
    }

    public function getHubSections(): array
    {
        return [
            [
                'title' => 'Directory',
                'cards' => [
                    $this->hubCard('Clients', ClientResource::class, Heroicon::OutlinedUsers, count: Client::query()->count()),
                    $this->hubCard('Documents', ClientDocumentResource::class, Heroicon::OutlinedDocumentText),
                    $this->hubCard('Tags', ClientTagResource::class, Heroicon::OutlinedTag),
                    $this->hubCard('Groups', ClientGroupResource::class, Heroicon::OutlinedRectangleGroup),
                ],
            ],
            [
                'title' => 'Portal & support',
                'cards' => [
                    $this->hubCard('Announcements', PortalAnnouncementResource::class, Heroicon::OutlinedMegaphone),
                    $this->hubCard('Support tickets', SupportTicketResource::class, Heroicon::OutlinedLifebuoy, count: SupportTicket::query()->count()),
                    $this->hubCard('Meetings', MeetingRequestResource::class, Heroicon::OutlinedCalendarDays),
                    $this->hubCard('Follow-ups', FollowUpResource::class, Heroicon::OutlinedBellAlert),
                    $this->hubCard('Activity log', DomainActivityResource::class, Heroicon::OutlinedClock),
                ],
            ],
        ];
    }
}
