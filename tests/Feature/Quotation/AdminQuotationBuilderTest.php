<?php

namespace Tests\Feature\Quotation;

use App\Core\Enums\ProjectType;
use App\Core\Enums\RoleType;
use App\Domains\Quotation\Services\PricingService;
use App\Domains\Quotation\Services\QuotationRequestService;
use App\Domains\Quotation\Services\QuotationService;
use App\Filament\Resources\QuotationRequests\Pages\ManageQuotationRequests;
use App\Filament\Resources\QuotationRequests\Pages\ViewQuotationRequest;
use App\Filament\Resources\Quotations\Pages\EditQuotation;
use App\Filament\Resources\Quotations\QuotationResource;
use App\Models\Quotation;
use App\Models\QuotationRequest;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class AdminQuotationBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->administrator()->create(['admin_onboarded_at' => now()]);
        $admin->assignRole(RoleType::Administrator->value);

        $this->actingAs($admin);
    }

    public function test_pricing_applies_discount_before_vat_and_skips_optional_lines(): void
    {
        $totals = app(PricingService::class)->summarize([
            ['quantity' => 2, 'unit_price' => 1000, 'is_optional' => false],
            ['quantity' => 1, 'unit_price' => 500, 'is_optional' => true],
        ], 16, 200);

        $this->assertSame([
            'subtotal' => 2000.0,
            'tax_amount' => 288.0,
            'discount_amount' => 200.0,
            'total_amount' => 2088.0,
        ], $totals);
    }

    public function test_request_list_and_view_pages_render(): void
    {
        $request = $this->submitRequest();

        Livewire::test(ManageQuotationRequests::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$request])
            ->assertTableColumnExists('phone')
            ->assertTableColumnExists('county')
            ->assertTableColumnExists('location')
            ->assertTableColumnExists('description')
            ->assertTableColumnExists('services.title');

        Livewire::test(ViewQuotationRequest::class, ['record' => $request->getKey()])
            ->assertOk()
            ->assertSee('Westlands')
            ->assertSee('Office fit-out for 40 staff.');
    }

    public function test_admin_prepares_prices_and_previews_quotation(): void
    {
        $request = $this->submitRequest();

        Livewire::test(ViewQuotationRequest::class, ['record' => $request->getKey()])
            ->callAction('create_quotation')
            ->assertRedirect();

        $quotation = Quotation::query()->where('quotation_request_id', $request->id)->firstOrFail();

        Livewire::test(EditQuotation::class, ['record' => $quotation->getKey()])
            ->assertOk()
            ->assertSee('Office fit-out for 40 staff.')
            ->fillForm([
                'tax_rate' => 16,
                'discount_amount' => 0,
                'terms' => "1. Valid for 14 days.\n2. 50% deposit.",
            ])
            ->set('data.items', [
                'a' => ['label' => 'Partitioning', 'description' => 'Glass partitions', 'quantity' => 10, 'unit' => 'sqm', 'unit_price' => 4500, 'is_optional' => false],
                'b' => ['label' => 'Paint', 'description' => null, 'quantity' => 1, 'unit' => 'lot', 'unit_price' => 5000, 'is_optional' => false],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $quotation->refresh();

        $this->assertSame('50000.00', $quotation->subtotal);
        $this->assertSame('8000.00', $quotation->tax_amount);
        $this->assertSame('58000.00', $quotation->total_amount);
        $this->assertSame('45000.00', $quotation->items()->where('label', 'Partitioning')->value('line_total'));

        $this->get(QuotationResource::pdfPreviewUrl($quotation))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_recalculate_refreshes_stale_line_totals(): void
    {
        $request = $this->submitRequest();
        $quotation = app(QuotationService::class)->createFromRequest($request);
        $quotation->items()->create(['label' => 'Stale', 'quantity' => 3, 'unit_price' => 100, 'line_total' => 1]);

        $quotation = app(QuotationService::class)->recalculate($quotation);

        $this->assertSame('300.00', $quotation->items()->where('label', 'Stale')->value('line_total'));
        $this->assertSame('348.00', $quotation->total_amount);
    }

    private function submitRequest(): QuotationRequest
    {
        return app(QuotationRequestService::class)->submit([
            'full_name' => 'Builder Client',
            'email' => 'builder@example.com',
            'phone' => '+254700000002',
            'project_type' => ProjectType::Commercial->value,
            'county' => 'Nairobi',
            'location' => 'Westlands',
            'description' => 'Office fit-out for 40 staff.',
        ]);
    }
}
