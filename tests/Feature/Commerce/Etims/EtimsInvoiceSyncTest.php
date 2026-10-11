<?php

namespace Tests\Feature\Commerce\Etims;

use App\Console\Commands\Etims\RetryFailedEtimsSyncs;
use App\Core\Enums\BudgetRange;
use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\EtimsSyncStatus;
use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\InvoiceType;
use App\Core\Enums\PreferredContactMethod;
use App\Core\Enums\PricingModel;
use App\Core\Enums\ProductStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\PurchaseMode;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Commerce\Actions\IssueCreditNote;
use App\Domains\Commerce\Services\Etims\EtimsSettingsService;
use App\Domains\Commerce\Services\SalesOrderService;
use App\Domains\Quotation\Services\QuotationRequestService;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Client;
use App\Models\Company;
use App\Models\EtimsSetting;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\QuotationRequest;
use App\Models\TaxRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class EtimsInvoiceSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_listener_syncs_invoice_on_success(): void
    {
        Mail::fake();
        $this->enableEtims();

        Http::fake([
            '*' => Http::response(['status' => '000', 'data' => [
                'rcptNo' => '123', 'sdcId' => 'SDC001', 'invcNo' => '456',
            ]]),
        ]);

        $invoice = $this->issuedInvoice();

        $this->assertSame(EtimsSyncStatus::Synced, $invoice->fresh()->kra_sync_status);
        $this->assertSame('123', $invoice->fresh()->kra_rcpt_no);
        $this->assertNotNull($invoice->fresh()->kra_request_payload);
    }

    public function test_listener_marks_failed_and_rethrows_on_error_response(): void
    {
        Mail::fake();
        $settings = $this->enableEtims();

        Http::fake([
            '*' => Http::response(['status' => '999', 'message' => 'boom']),
        ]);

        $invoice = $this->draftInvoiceForAcceptedQuote();

        $this->expectException(RuntimeException::class);

        try {
            app(SalesOrderService::class)->issueInvoice($invoice);
        } finally {
            $fresh = $invoice->fresh();
            $this->assertSame(EtimsSyncStatus::Failed, $fresh->kra_sync_status);
            $this->assertNotNull($fresh->kra_last_error);
        }
    }

    public function test_sync_is_skipped_when_etims_disabled(): void
    {
        Mail::fake();

        Http::fake();

        $invoice = $this->draftInvoiceForAcceptedQuote();
        $issued = app(SalesOrderService::class)->issueInvoice($invoice);

        Http::assertNothingSent();
        $this->assertSame(EtimsSyncStatus::NotSynced, $issued->fresh()->kra_sync_status);
    }

    public function test_issue_credit_note_fails_to_sync_when_parent_was_never_synced_to_etims(): void
    {
        // IssueCreditNote itself no longer requires the parent to be
        // eTIMS-synced (that gate moved — see InvoiceVoidAndCreditNoteTest
        // for the "eTIMS disabled" success path). But if eTIMS IS enabled,
        // the credit note's own sync attempt still fails via
        // EtimsSalesApiService::sendCreditNote()'s guard, rolling back the
        // whole credit note creation (same DB transaction).
        Mail::fake();
        $this->enableEtims();

        Http::fake(['*' => Http::response(['status' => '999', 'message' => 'boom'])]);

        $invoice = $this->draftInvoiceForAcceptedQuote();

        try {
            app(SalesOrderService::class)->issueInvoice($invoice);
        } catch (RuntimeException) {
            // expected: sync fails, invoice stays not-synced
        }

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('has not been synced to eTIMS');

        app(IssueCreditNote::class)->handle($invoice->fresh());
    }

    public function test_issue_credit_note_creates_reversal_and_prevents_duplicates(): void
    {
        Mail::fake();
        $this->enableEtims();

        Http::fake([
            '*' => Http::response(['status' => '000', 'data' => [
                'rcptNo' => '1', 'sdcId' => 'SDC1', 'invcNo' => '1',
            ]]),
        ]);

        $invoice = $this->issuedInvoice();

        $creditNote = app(IssueCreditNote::class)->handle($invoice->fresh());

        $this->assertSame(InvoiceType::CreditNote, $creditNote->type);
        $this->assertSame($invoice->id, $creditNote->parent_invoice_id);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('already been issued');

        app(IssueCreditNote::class)->handle($invoice->fresh());
    }

    public function test_quotation_tax_rate_override_wins_over_inferred_kra_band(): void
    {
        Mail::fake();
        $this->enableEtims();

        $exempt = TaxRate::query()->create([
            'code' => 'EXEMPT-TEST',
            'name' => 'Exempt (test)',
            'rate' => 16.00,
            'kra_tax_type_code' => 'A',
        ]);

        $capturedPayload = null;
        Http::fake(function ($request) use (&$capturedPayload) {
            $capturedPayload = $request->data();

            return Http::response(['status' => '000', 'data' => [
                'rcptNo' => '1', 'sdcId' => 'SDC1', 'invcNo' => '1',
            ]]);
        });

        $invoice = $this->draftInvoiceForAcceptedQuote();
        $invoice->quotation()->update(['tax_rate_id' => $exempt->id]);

        app(SalesOrderService::class)->issueInvoice($invoice);

        $this->assertNotNull($capturedPayload);
        $this->assertSame('A', $capturedPayload['itemList'][0]['taxTyCd']);
    }

    public function test_retry_command_only_picks_up_unsynced_issued_invoices(): void
    {
        Mail::fake();
        $this->enableEtims();

        // A single fake for the whole test: Http::fake() appends rather than
        // replaces stub callbacks within one test method, so re-faking a
        // second '*' pattern later would still match the first-registered
        // (older) stub. Simulate "a previously-failed sync" by forcing the
        // status directly instead of faking a failure response first.
        Http::fake([
            '*' => Http::response(['status' => '000', 'data' => [
                'rcptNo' => '9', 'sdcId' => 'SDC9', 'invcNo' => '9',
            ]]),
        ]);

        $invoice = $this->issuedInvoice();
        $this->assertSame(EtimsSyncStatus::Synced, $invoice->kra_sync_status);

        $invoice->forceFill([
            'kra_sync_status' => EtimsSyncStatus::Failed,
            'kra_request_payload' => null,
            'kra_synced_at' => null,
        ])->save();

        $this->artisan(RetryFailedEtimsSyncs::class)->assertExitCode(0);

        $this->assertSame(EtimsSyncStatus::Synced, $invoice->fresh()->kra_sync_status);
    }

    private function enableEtims(): EtimsSetting
    {
        return app(EtimsSettingsService::class)->update([
            'enabled' => true,
            'device_serial' => 'DEV-001',
            'base_url_override' => 'http://etims-middleware.test',
            'default_item_classification_code' => '83111801',
            'default_item_code' => 'ITEM-001',
        ]);
    }

    private function issuedInvoice(): Invoice
    {
        $invoice = $this->draftInvoiceForAcceptedQuote();

        return app(SalesOrderService::class)->issueInvoice($invoice)->fresh();
    }

    private function draftInvoiceForAcceptedQuote(): Invoice
    {
        Company::query()->create([
            'name' => 'Zytech Contractors',
            'slug' => 'zytech-contractors-etims-test-'.uniqid(),
            'kra_pin' => 'P051234567X',
            'status' => 'published',
        ]);

        Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Prospect,
            'name' => 'Invoice Client',
            'kra_pin' => 'P000111222C',
            'email' => 'etims-invoice-'.uniqid().'@example.com',
        ]);

        $category = ProductCategory::query()->create([
            'name' => 'Events',
            'slug' => 'events-cat-etims-'.uniqid(),
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $product = Product::query()->create([
            'product_category_id' => $category->id,
            'title' => 'Event Bolt',
            'slug' => 'event-bolt-etims-'.uniqid(),
            'sku' => 'EVT-BOLT-ETIMS',
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
            'email' => 'etims-invoice-request-'.uniqid().'@example.com',
            'phone' => '+254700000007',
            'project_type' => ProjectType::Residential->value,
            'county' => 'Nairobi',
            'location' => 'Westlands',
            'budget_range' => BudgetRange::Undecided->value,
            'preferred_contact_method' => PreferredContactMethod::Email->value,
            'description' => 'Need materials for eTIMS test.',
        ], [], [$product->id]);

        $quotation = app(QuotationService::class)->createFromRequest($request->fresh());
        $quotation = app(QuotationService::class)->approve($quotation);
        $quotation = app(QuotationService::class)->send($quotation);
        $accepted = app(QuotationService::class)->accept($quotation->fresh());

        $invoice = Invoice::query()->where('quotation_id', $accepted->id)->firstOrFail();
        $this->assertSame(InvoiceStatus::Draft, $invoice->status);

        return $invoice;
    }
}
