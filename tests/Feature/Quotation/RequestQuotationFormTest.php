<?php

namespace Tests\Feature\Quotation;

use App\Domains\Website\Livewire\RequestQuotationForm;
use App\Models\QuotationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class RequestQuotationFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_submits_without_budget_timeline_or_contact_fields(): void
    {
        Mail::fake();

        Livewire::test(RequestQuotationForm::class)
            ->assertDontSee('Budget range')
            ->assertDontSee('Estimated timeline')
            ->assertDontSee('Products of interest')
            ->assertDontSee('Preferred contact')
            ->set('fullName', 'Form Client')
            ->set('email', 'form@example.com')
            ->set('projectType', 'commercial')
            ->set('county', 'Kiambu')
            ->set('location', 'Ruaka')
            ->set('description', 'New perimeter wall.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect();

        $request = QuotationRequest::query()->where('email', 'form@example.com')->firstOrFail();

        $this->assertNull($request->budget_range);
        $this->assertNull($request->estimated_timeline);
        $this->assertSame('Ruaka', $request->location);
    }
}
