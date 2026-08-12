<?php

namespace Database\Seeders;

use App\Core\Enums\AnnouncementType;
use App\Core\Enums\NotificationChannel;
use App\Domains\Communication\Services\AnnouncementService;
use App\Domains\Communication\Services\TemplateService;
use App\Models\Announcement;
use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class CommunicationSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'welcome',
                'name' => 'Welcome email',
                'channel' => NotificationChannel::Mail,
                'subject' => 'Welcome to Zytech, {{name}}',
                'body' => "Hi {{name}},\n\nYour Zytech account is ready. Sign in to track quotations and project updates.\n\n{{message}}",
            ],
            [
                'key' => 'quotation-submitted',
                'name' => 'Quotation request received',
                'channel' => NotificationChannel::Mail,
                'subject' => 'We received your quote request {{reference}}',
                'body' => "Hi {{name}},\n\nThanks for requesting a quotation. Your reference is {{reference}}.\n\nOur team will review the details and follow up shortly.",
            ],
            [
                'key' => 'quotation-sent',
                'name' => 'Quotation sent',
                'channel' => NotificationChannel::Mail,
                'subject' => 'Your quotation {{reference}} is ready',
                'body' => "Hi {{name}},\n\nYour quotation {{reference}} is ready to review.\n\n{{message}}",
            ],
            [
                'key' => 'portal-message',
                'name' => 'Portal message notice',
                'channel' => NotificationChannel::Mail,
                'subject' => 'New message from Zytech',
                'body' => "Hi {{name}},\n\nYou have a new portal message:\n\n{{message}}",
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::query()->updateOrCreate(
                ['key' => $template['key']],
                [...$template, 'is_active' => true],
            );
        }

        app(TemplateService::class)->forget();

        if (! Announcement::query()->where('title', 'Platform communication hub is live')->exists()) {
            app(AnnouncementService::class)->publish([
                'title' => 'Platform communication hub is live',
                'body' => 'Email delivery now runs through the Communication Hub with Resend support. Templates and delivery logs are managed in Filament.',
                'type' => AnnouncementType::System,
                'show_on_website' => true,
                'show_in_portal' => true,
                'sort_order' => 0,
            ]);
        }
    }
}
