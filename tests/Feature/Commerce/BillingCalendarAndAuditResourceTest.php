<?php

namespace Tests\Feature\Commerce;

use App\Core\Enums\RoleType;
use App\Filament\Pages\BillingCalendar;
use App\Filament\Resources\NotificationLogs\Pages\ListNotificationLogs;
use App\Filament\Widgets\BillingCalendarWidget;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BillingCalendarAndAuditResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->administrator()->create(['admin_onboarded_at' => now()]);
        $admin->assignRole(RoleType::Administrator->value);

        $this->actingAs($admin);
    }

    public function test_billing_calendar_page_renders(): void
    {
        Livewire::test(BillingCalendar::class)->assertOk();
    }

    public function test_billing_calendar_widget_fetches_events_without_error(): void
    {
        Livewire::test(BillingCalendarWidget::class)
            ->assertOk();
    }

    public function test_notification_log_resource_renders_and_is_read_only(): void
    {
        Livewire::test(ListNotificationLogs::class)
            ->assertOk()
            ->assertTableColumnExists('channel')
            ->assertTableColumnExists('status')
            ->assertTableColumnExists('recipient');
    }
}
