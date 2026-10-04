<?php

namespace Tests\Feature\Quotation;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\ProjectType;
use App\Core\Enums\QuotationStatus;
use App\Core\Enums\RoleType;
use App\Domains\Portal\Livewire\Quotations;
use App\Domains\Quotation\Services\QuotationRequestService;
use App\Domains\Quotation\Services\QuotationService;
use App\Filament\Resources\Quotations\Pages\EditQuotation;
use App\Models\Client;
use App\Models\Quotation;
use App\Models\User;
use Database\Seeders\ConfigurationSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class PortalQuotationAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $clientUser;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        $this->seed([RolePermissionSeeder::class, ConfigurationSeeder::class]);

        $this->admin = User::factory()->administrator()->create(['admin_onboarded_at' => now()]);
        $this->admin->assignRole(RoleType::Administrator->value);

        $this->clientUser = User::factory()->create(['email' => 'portal@example.com', 'email_verified_at' => now()]);
        $this->clientUser->assignRole(RoleType::Client->value);

        Client::query()->create([
            'user_id' => $this->clientUser->id,
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Portal Client',
            'email' => 'portal@example.com',
            'portal_access_granted_at' => now(),
        ]);
    }

    public function test_draft_is_hidden_until_sent_then_client_can_accept(): void
    {
        $quotation = $this->draftQuotation();

        $this->actingAs($this->clientUser);

        Livewire::test(Quotations::class)
            ->assertSee('Being prepared')
            ->assertDontSee('Accept quotation')
            ->assertDontSee('View PDF');

        $this->get(route('portal.quotations.pdf', $quotation))->assertNotFound();

        $this->actingAs($this->admin);

        Livewire::test(EditQuotation::class, ['record' => $quotation->getKey()])
            ->callAction('send_to_client')
            ->assertNotified('Quotation sent');

        $this->assertSame(QuotationStatus::Sent, $quotation->refresh()->status);
        $this->assertNotNull($quotation->sent_at);

        $this->actingAs($this->clientUser);

        $this->get(route('portal.quotations.pdf', $quotation))->assertOk();

        Livewire::test(Quotations::class)
            ->assertSee('Accept quotation')
            ->call('accept', $quotation->id);

        $this->assertSame(QuotationStatus::Accepted, $quotation->refresh()->status);
    }

    public function test_send_is_blocked_without_priced_items(): void
    {
        $quotation = $this->draftQuotation(priced: false);

        $this->actingAs($this->admin);

        Livewire::test(EditQuotation::class, ['record' => $quotation->getKey()])
            ->callAction('send_to_client')
            ->assertNotified('Not sent');

        $this->assertSame(QuotationStatus::Draft, $quotation->refresh()->status);
        $this->assertNull($quotation->sent_at);
    }

    private function draftQuotation(bool $priced = true): Quotation
    {
        $request = app(QuotationRequestService::class)->submit([
            'full_name' => 'Portal Client',
            'email' => 'portal@example.com',
            'project_type' => ProjectType::Residential->value,
            'county' => 'Nairobi',
            'description' => 'Kitchen remodel.',
        ]);

        $this->actingAs($this->admin);
        $quotation = app(QuotationService::class)->createFromRequest($request);

        if ($priced) {
            $quotation->items()->create(['label' => 'Cabinets', 'quantity' => 1, 'unit_price' => 120000]);
            app(QuotationService::class)->recalculate($quotation);
        }

        return $quotation->refresh();
    }
}
