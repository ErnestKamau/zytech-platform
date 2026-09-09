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
                'body' => "Hi {{name}},\n\nYour quotation {{reference}} is ready to review in the client portal. You can accept, reject, or request a revision there.\n\n{{message}}",
            ],
            [
                'key' => 'quotation-accepted',
                'name' => 'Quotation accepted',
                'channel' => NotificationChannel::Mail,
                'subject' => 'Quotation {{reference}} accepted',
                'body' => "Hi {{name}},\n\nThanks — we have recorded your acceptance of quotation {{reference}}.\n\n{{message}}",
            ],
            [
                'key' => 'quotation-rejected',
                'name' => 'Quotation rejected',
                'channel' => NotificationChannel::Mail,
                'subject' => 'Quotation {{reference}} declined',
                'body' => "Hi {{name}},\n\nWe have recorded that quotation {{reference}} was declined.\n\n{{message}}",
            ],
            [
                'key' => 'order-placed',
                'name' => 'Order placed',
                'channel' => NotificationChannel::Mail,
                'subject' => 'Order {{reference}} received',
                'body' => "Hi {{name}},\n\nThanks for your order {{reference}}. Total: {{total}}.\n\n{{message}}",
            ],
            [
                'key' => 'order-cancelled',
                'name' => 'Order cancelled',
                'channel' => NotificationChannel::Mail,
                'subject' => 'Order {{reference}} cancelled',
                'body' => "Hi {{name}},\n\nYour order {{reference}} has been cancelled.\n\n{{message}}",
            ],
            [
                'key' => 'order-status-changed',
                'name' => 'Order status update',
                'channel' => NotificationChannel::Mail,
                'subject' => 'Order {{reference}} is now {{status}}',
                'body' => "Hi {{name}},\n\nYour order {{reference}} status is now {{status}}.\n\n{{message}}",
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
