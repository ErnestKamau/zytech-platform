<?php

namespace Tests\Feature\Quotation;

use App\Core\Enums\BudgetRange;
use App\Core\Enums\PreferredContactMethod;
use App\Core\Enums\ProjectType;
use App\Domains\Quotation\Services\QuotationRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TrackQuotationRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_track_page_shows_submitted_request_with_status_history(): void
    {
        Mail::fake();

        $request = app(QuotationRequestService::class)->submit([
            'full_name' => 'Track Client',
            'email' => 'track@example.com',
            'project_type' => ProjectType::Commercial->value,
            'county' => 'Nairobi',
            'budget_range' => BudgetRange::Undecided->value,
            'preferred_contact_method' => PreferredContactMethod::Email->value,
            'description' => 'Office fit-out.',
        ]);

        $this->get(route('quote.track', ['reference' => $request->reference_number]))
            ->assertOk()
            ->assertSee($request->reference_number);
    }
}
