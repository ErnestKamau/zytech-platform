<?php

namespace Tests\Feature\Quotation;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\QuotationStatus;
use App\Core\Enums\QuotationType;
use App\Domains\Quotation\Services\PricingService;
use App\Models\Client;
use App\Models\Quotation;
use App\Models\TaxRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxRateLookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_pricing_service_falls_back_to_default_tax_rate_when_none_linked(): void
    {
        $quotation = $this->draftQuotation();

        $totals = app(PricingService::class)->totals($quotation);

        $this->assertSame(16.0, round($totals['tax_amount'] / $totals['subtotal'] * 100, 2));
    }

    public function test_selecting_a_zero_rated_tax_rate_zeroes_out_tax(): void
    {
        // Mirrors what the Filament Select's afterStateUpdated callback does:
        // picking a TaxRate syncs its rate onto the tax_rate snapshot column.
        $zeroRate = TaxRate::query()->where('code', 'ZERO')->firstOrFail();

        $quotation = $this->draftQuotation();
        $quotation->forceFill(['tax_rate' => $zeroRate->rate, 'tax_rate_id' => $zeroRate->id])->save();

        $totals = app(PricingService::class)->totals($quotation->fresh());

        $this->assertSame(0.0, $totals['tax_amount']);
    }

    public function test_only_one_tax_rate_can_be_default(): void
    {
        $zeroRate = TaxRate::query()->where('code', 'ZERO')->firstOrFail();
        $vat16 = TaxRate::query()->where('code', 'VAT16')->firstOrFail();

        $this->assertTrue($vat16->is_default);

        $zeroRate->update(['is_default' => true]);

        $this->assertFalse($vat16->fresh()->is_default);
        $this->assertTrue($zeroRate->fresh()->is_default);
    }

    private function draftQuotation(): Quotation
    {
        $client = Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Prospect,
            'name' => 'Tax Test Client',
            'email' => 'tax-rate-'.uniqid().'@example.com',
        ]);

        $quotation = Quotation::query()->create([
            'reference_number' => 'ZQ-TAXTEST-'.uniqid(),
            'client_id' => $client->id,
            'title' => 'Tax rate test quotation',
            'type' => QuotationType::Standard,
            'status' => QuotationStatus::Draft,
            'subtotal' => 1000,
            'tax_rate' => 16,
            'tax_amount' => 160,
            'discount_amount' => 0,
            'total_amount' => 1160,
            'currency' => 'KES',
            'revision_number' => 1,
        ]);

        $quotation->items()->create([
            'label' => 'Line item',
            'quantity' => 1,
            'unit_price' => 1000,
            'line_total' => 1000,
            'sort_order' => 0,
        ]);

        return $quotation->fresh();
    }
}
