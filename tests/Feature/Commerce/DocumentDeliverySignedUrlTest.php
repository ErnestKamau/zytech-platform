<?php

namespace Tests\Feature\Commerce;

use App\Core\Enums\BudgetRange;
use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\PreferredContactMethod;
use App\Core\Enums\PricingModel;
use App\Core\Enums\ProductStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\PurchaseMode;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Quotation\Services\QuotationRequestService;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProformaInvoice;
use App\Models\QuotationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class DocumentDeliverySignedUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_unsigned_request_is_rejected(): void
    {
        $proforma = $this->acceptedProforma();

        $this->get(route('documents.proforma', ['proformaInvoice' => $proforma]))
            ->assertForbidden();
    }

    public function test_correctly_signed_request_within_ttl_succeeds(): void
    {
        $proforma = $this->acceptedProforma();

        $url = URL::temporarySignedRoute('documents.proforma', now()->addHours(24), ['proformaInvoice' => $proforma->id]);

        $this->get($url)
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_expired_signature_is_rejected(): void
    {
        $proforma = $this->acceptedProforma();

        $url = URL::temporarySignedRoute('documents.proforma', now()->subMinutes(5), ['proformaInvoice' => $proforma->id]);

        $this->get($url)->assertForbidden();
    }

    public function test_tampered_signature_is_rejected(): void
    {
        $proforma = $this->acceptedProforma();

        $url = URL::temporarySignedRoute('documents.proforma', now()->addHours(24), ['proformaInvoice' => $proforma->id]);
        $tampered = $url.'&tampered=1';

        $this->get($tampered)->assertForbidden();
    }

    private function acceptedProforma(): ProformaInvoice
    {
        Mail::fake();

        Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Prospect,
            'name' => 'Signed URL Client',
            'email' => 'signed-url@example.com',
        ]);

        $category = ProductCategory::query()->create([
            'name' => 'Signed URL Cat',
            'slug' => 'signed-url-cat',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $product = Product::query()->create([
            'product_category_id' => $category->id,
            'title' => 'Signed URL Item',
            'slug' => 'signed-url-item',
            'sku' => 'SU-1',
            'purchase_mode' => PurchaseMode::Both,
            'status' => ProductStatus::Published,
            'visibility' => VisibilityStatus::Public,
            'pricing_model' => PricingModel::Fixed,
            'price_amount' => 200,
            'price_currency' => 'KES',
            'published_at' => now(),
            'sort_order' => 1,
        ]);

        /** @var QuotationRequest $request */
        $request = app(QuotationRequestService::class)->submit([
            'full_name' => 'Signed URL Client',
            'email' => 'signed-url@example.com',
            'phone' => '+254700000005',
            'project_type' => ProjectType::Residential->value,
            'county' => 'Nairobi',
            'location' => 'Westlands',
            'budget_range' => BudgetRange::Undecided->value,
            'preferred_contact_method' => PreferredContactMethod::Email->value,
            'description' => 'Signed URL test.',
        ], [], [$product->id]);

        $quotation = app(QuotationService::class)->createFromRequest($request->fresh());
        $quotation = app(QuotationService::class)->approve($quotation);
        $quotation = app(QuotationService::class)->send($quotation);
        $accepted = app(QuotationService::class)->accept($quotation->fresh());

        return ProformaInvoice::query()->where('quotation_id', $accepted->id)->firstOrFail();
    }
}
