<?php

namespace Tests\Feature\Commerce;

use App\Core\Enums\BudgetRange;
use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\PreferredContactMethod;
use App\Core\Enums\PricingModel;
use App\Core\Enums\ProductStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\PurchaseMode;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Commerce\Services\SalesOrderService;
use App\Domains\Company\Services\CompanyService;
use App\Domains\Quotation\Services\QuotationRequestService;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Client;
use App\Models\ClientTimeline;
use App\Models\Company;
use App\Models\DomainActivity;
use App\Models\Invoice;
use App\Models\InvoiceDocument;
use App\Models\NotificationLog;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\QuotationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvoiceBusinessEventSliceTest extends TestCase
{
    use RefreshDatabase;

    public function test_issuing_invoice_dispatches_pdf_and_notifications(): void
    {
        Mail::fake();

        $invoice = $this->draftInvoiceForAcceptedQuote();

        $issued = app(SalesOrderService::class)->issueInvoice($invoice);

        $this->assertSame(InvoiceStatus::Issued, $issued->status);
        $this->assertNotNull($issued->issued_at);
        $this->assertNotNull($issued->due_date);

        $this->assertTrue(InvoiceDocument::query()->where('invoice_id', $issued->id)->exists());
        $this->assertTrue(
            NotificationLog::query()
                ->where('type', 'invoice-issued')
                ->where('meta->invoice_id', $issued->id)
                ->exists()
        );
        $this->assertTrue(
            DomainActivity::query()
                ->where('subject_id', $issued->id)
                ->where('event', 'invoice.issued')
                ->exists()
        );
        $this->assertTrue(
            ClientTimeline::query()
                ->where('meta->invoice_id', $issued->id)
                ->where('event_type', 'invoice-issued')
                ->exists()
        );
    }

    public function test_issue_invoice_is_a_noop_for_non_draft_invoices(): void
    {
        Mail::fake();

        $invoice = $this->draftInvoiceForAcceptedQuote();
        $issued = app(SalesOrderService::class)->issueInvoice($invoice);
        $issuedAt = $issued->issued_at;

        $again = app(SalesOrderService::class)->issueInvoice($issued->fresh());

        $this->assertSame(InvoiceStatus::Issued, $again->status);
        $this->assertTrue($issuedAt->equalTo($again->issued_at));
        $this->assertSame(
            1,
            DomainActivity::query()->where('subject_id', $issued->id)->where('event', 'invoice.issued')->count(),
        );
    }

    public function test_kra_pin_and_vat_number_render_on_invoice_pdf(): void
    {
        Mail::fake();

        Company::query()->create([
            'name' => 'Zytech Contractors',
            'slug' => 'zytech-contractors-invoice-test',
            'kra_pin' => 'P051234567X',
            'vat_number' => 'VAT9988776',
            'status' => 'published',
        ]);

        $invoice = $this->draftInvoiceForAcceptedQuote();
        $invoice->client?->update(['kra_pin' => 'P000111222C']);
        $invoice = $invoice->fresh(['client', 'quotation', 'salesOrder', 'items', 'payments']);

        $html = view('pdf.invoice', [
            'invoice' => $invoice,
            'company' => app(CompanyService::class)->current(),
        ])->render();

        $this->assertStringContainsString('P051234567X', $html);
        $this->assertStringContainsString('VAT9988776', $html);
        $this->assertStringContainsString('P000111222C', $html);
        $this->assertStringContainsString('TAX INVOICE', $html);
    }

    private function draftInvoiceForAcceptedQuote(): Invoice
    {
        Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Prospect,
            'name' => 'Invoice Client',
            'email' => 'invoice-events@example.com',
        ]);

        $category = ProductCategory::query()->create([
            'name' => 'Events',
            'slug' => 'events-cat-invoice',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $product = Product::query()->create([
            'product_category_id' => $category->id,
            'title' => 'Event Bolt',
            'slug' => 'event-bolt-invoice',
            'sku' => 'EVT-BOLT-INV',
            'purchase_mode' => PurchaseMode::Both,
            'status' => ProductStatus::Published,
            'visibility' => VisibilityStatus::Public,
            'pricing_model' => PricingModel::Fixed,
            'price_amount' => 100,
            'price_currency' => 'KES',
            'published_at' => now(),
            'sort_order' => 1,
        ]);

        /** @var QuotationRequest $request */
        $request = app(QuotationRequestService::class)->submit([
            'full_name' => 'Invoice Client',
            'email' => 'invoice-events@example.com',
            'phone' => '+254700000006',
            'project_type' => ProjectType::Residential->value,
            'county' => 'Nairobi',
            'location' => 'Westlands',
            'budget_range' => BudgetRange::Undecided->value,
            'preferred_contact_method' => PreferredContactMethod::Email->value,
            'description' => 'Need materials for invoice test.',
        ], [], [$product->id]);

        $quotation = app(QuotationService::class)->createFromRequest($request->fresh());
        $quotation = app(QuotationService::class)->approve($quotation);
        $quotation = app(QuotationService::class)->send($quotation);
        $accepted = app(QuotationService::class)->accept($quotation->fresh());

        return Invoice::query()->where('quotation_id', $accepted->id)->firstOrFail();
    }
}
