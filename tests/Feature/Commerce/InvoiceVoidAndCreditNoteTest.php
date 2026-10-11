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
use App\Domains\Commerce\Actions\IssueCreditNote;
use App\Domains\Commerce\Actions\VoidInvoice;
use App\Domains\Commerce\Exceptions\PaymentException;
use App\Domains\Commerce\Services\PaymentService;
use App\Domains\Commerce\Services\SalesOrderService;
use App\Domains\Quotation\Services\QuotationRequestService;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\QuotationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class InvoiceVoidAndCreditNoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_invoice_can_be_voided(): void
    {
        Mail::fake();
        $invoice = $this->draftInvoiceForAcceptedQuote();

        $voided = app(VoidInvoice::class)->handle($invoice, 'Created in error');

        $this->assertSame(InvoiceStatus::Void, $voided->status);
        $this->assertNotNull($voided->voided_at);
        $this->assertSame('Created in error', $voided->void_reason);
    }

    public function test_issued_unpaid_invoice_can_be_voided(): void
    {
        Mail::fake();
        $invoice = $this->issuedInvoice();

        $voided = app(VoidInvoice::class)->handle($invoice, 'Client cancelled order');

        $this->assertSame(InvoiceStatus::Void, $voided->status);
    }

    public function test_invoice_with_a_payment_cannot_be_voided(): void
    {
        Mail::fake();
        $invoice = $this->issuedInvoice();

        app(PaymentService::class)->recordForInvoice($invoice, ['amount' => 10]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('cannot be voided');

        app(VoidInvoice::class)->handle($invoice->fresh(), 'Should fail');
    }

    public function test_payment_cannot_be_recorded_against_a_void_invoice(): void
    {
        Mail::fake();
        $invoice = $this->issuedInvoice();

        app(VoidInvoice::class)->handle($invoice, 'No longer needed');

        $this->expectException(PaymentException::class);

        app(PaymentService::class)->recordForInvoice($invoice->fresh(), ['amount' => 10]);
    }

    public function test_credit_note_can_be_issued_against_an_issued_invoice_without_etims(): void
    {
        Mail::fake();
        $invoice = $this->issuedInvoice();

        $creditNote = app(IssueCreditNote::class)->handle($invoice);

        $this->assertSame(InvoiceStatus::Issued, $creditNote->status);
        $this->assertSame($invoice->id, $creditNote->parent_invoice_id);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('already been issued');

        app(IssueCreditNote::class)->handle($invoice->fresh());
    }

    public function test_credit_note_cannot_be_issued_against_a_draft_invoice(): void
    {
        Mail::fake();
        $invoice = $this->draftInvoiceForAcceptedQuote();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('has not been issued');

        app(IssueCreditNote::class)->handle($invoice);
    }

    private function issuedInvoice(): Invoice
    {
        $invoice = $this->draftInvoiceForAcceptedQuote();

        return app(SalesOrderService::class)->issueInvoice($invoice)->fresh();
    }

    private function draftInvoiceForAcceptedQuote(): Invoice
    {
        Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Prospect,
            'name' => 'Invoice Client',
            'email' => 'void-cn-'.uniqid().'@example.com',
        ]);

        $category = ProductCategory::query()->create([
            'name' => 'Events',
            'slug' => 'events-cat-void-'.uniqid(),
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $product = Product::query()->create([
            'product_category_id' => $category->id,
            'title' => 'Event Bolt',
            'slug' => 'event-bolt-void-'.uniqid(),
            'sku' => 'EVT-BOLT-VOID',
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
            'email' => 'void-cn-request-'.uniqid().'@example.com',
            'phone' => '+254700000008',
            'project_type' => ProjectType::Residential->value,
            'county' => 'Nairobi',
            'location' => 'Westlands',
            'budget_range' => BudgetRange::Undecided->value,
            'preferred_contact_method' => PreferredContactMethod::Email->value,
            'description' => 'Need materials for void/credit-note test.',
        ], [], [$product->id]);

        $quotation = app(QuotationService::class)->createFromRequest($request->fresh());
        $quotation = app(QuotationService::class)->approve($quotation);
        $quotation = app(QuotationService::class)->send($quotation);
        $accepted = app(QuotationService::class)->accept($quotation->fresh());

        return Invoice::query()->where('quotation_id', $accepted->id)->firstOrFail();
    }
}
