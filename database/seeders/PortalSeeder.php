<?php

namespace Database\Seeders;

use App\Core\Enums\DocumentVisibility;
use App\Core\Enums\MeetingType;
use App\Core\Enums\PortalNotificationType;
use App\Domains\Client\Services\ClientService;
use App\Domains\Portal\Services\MeetingService;
use App\Domains\Portal\Services\MessageService;
use App\Domains\Portal\Services\NotificationService;
use App\Domains\Portal\Services\SupportService;
use App\Models\Client;
use App\Models\MeetingSlot;
use App\Models\PortalAnnouncement;
use App\Models\User;
use Illuminate\Database\Seeder;

class PortalSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'client@zytech.local')->first();
        $client = Client::query()->where('email', 'james.mwangi@example.com')->first();

        if ($user !== null && $client !== null) {
            if ($client->user_id !== $user->id || $client->portal_access_granted_at === null) {
                app(ClientService::class)->assignPortalAccess($client, $user->id);
            }

            $client->documents()->where('title', 'Signed contract — Karen driveway')->update([
                'visibility' => DocumentVisibility::Client,
            ]);

            if ($client->conversations()->doesntExist()) {
                app(MessageService::class)->open(
                    $client,
                    'Welcome to your portal',
                    'Thanks for working with Zytech. Use Messages for project questions and Support for account help.',
                    $user,
                );
            }

            if ($client->supportTickets()->doesntExist()) {
                app(SupportService::class)->open(
                    $client,
                    $user,
                    'Confirm site access hours',
                    'Please confirm preferred working hours for the Karen driveway works.',
                );
            }

            if ($client->portalNotifications()->doesntExist()) {
                app(NotificationService::class)->create(
                    $client,
                    PortalNotificationType::Announcement,
                    'Portal is ready',
                    'Your client workspace is live. Review quotations, documents, and project updates here.',
                );
            }

            if ($client->meetingRequests()->doesntExist()) {
                $slot = MeetingSlot::query()->create([
                    'meeting_type' => MeetingType::SiteVisit,
                    'starts_at' => now()->addDays(5)->setTime(10, 0),
                    'ends_at' => now()->addDays(5)->setTime(11, 0),
                    'is_available' => true,
                ]);

                app(MeetingService::class)->schedule(
                    $client,
                    MeetingType::SiteVisit,
                    'Prefer morning visit if possible.',
                    $slot->id,
                );
            }
        }

        MeetingSlot::query()->firstOrCreate(
            [
                'starts_at' => now()->addDays(7)->setTime(14, 0),
                'ends_at' => now()->addDays(7)->setTime(15, 0),
            ],
            [
                'meeting_type' => MeetingType::Consultation,
                'is_available' => true,
            ],
        );

        PortalAnnouncement::query()->updateOrCreate(
            ['title' => 'Welcome to the Zytech Client Portal'],
            [
                'body' => 'Track quotations, download shared documents, message the team, and request meetings from one place.',
                'is_published' => true,
                'published_at' => now(),
                'sort_order' => 0,
            ],
        );
    }
}
