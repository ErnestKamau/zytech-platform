<?php

namespace Tests\Feature\Portal;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\ProjectType;
use App\Core\Enums\RoleType;
use App\Domains\Portal\Livewire\QuotationShow;
use App\Domains\Quotation\Services\QuotationRequestService;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Client;
use App\Models\ClientTimeline;
use App\Models\User;
use Database\Seeders\ConfigurationSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class QuotationTimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_timeline_rows_are_created_at_each_transition_and_rendered_on_the_detail_page(): void
    {
        Mail::fake();
        $this->seed([RolePermissionSeeder::class, ConfigurationSeeder::class]);

        $admin = User::factory()->administrator()->create(['admin_onboarded_at' => now()]);
        $admin->assignRole(RoleType::Administrator->value);

        $clientUser = User::factory()->create(['email' => 'timeline@example.com', 'email_verified_at' => now()]);
        $clientUser->assignRole(RoleType::Client->value);

        Client::query()->create([
            'user_id' => $clientUser->id,
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Timeline Client',
            'email' => 'timeline@example.com',
            'portal_access_granted_at' => now(),
        ]);

        $this->actingAs($admin);

        $request = app(QuotationRequestService::class)->submit([
            'full_name' => 'Timeline Client',
            'email' => 'timeline@example.com',
            'project_type' => ProjectType::Residential->value,
            'county' => 'Nairobi',
            'description' => 'Bathroom remodel.',
        ]);

        $quotation = app(QuotationService::class)->createFromRequest($request);
        $quotation->items()->create(['label' => 'Tiling', 'quantity' => 1, 'unit_price' => 80000]);
        $quotation = app(QuotationService::class)->recalculate($quotation);
        $quotation = app(QuotationService::class)->approve($quotation);
        $quotation = app(QuotationService::class)->send($quotation->fresh());

        $this->assertTrue(
            ClientTimeline::query()
                ->where('meta->quotation_id', $quotation->id)
                ->where('event_type', 'quotation-sent')
                ->exists()
        );

        $accepted = app(QuotationService::class)->accept($quotation->fresh());

        $this->assertTrue(
            ClientTimeline::query()
                ->where('meta->quotation_id', $accepted->id)
                ->where('event_type', 'quotation-accepted')
                ->exists()
        );
        $this->assertTrue(
            ClientTimeline::query()
                ->where('meta->quotation_id', $accepted->id)
                ->where('event_type', 'proforma-invoice-issued')
                ->exists()
        );

        $this->actingAs($clientUser);

        Livewire::test(QuotationShow::class, ['quotation' => $accepted->id])
            ->assertOk()
            ->assertSee('Quotation sent')
            ->assertSee('Quotation accepted')
            ->assertSee('Proforma invoice issued');
    }
}
