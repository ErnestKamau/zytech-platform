<?php

namespace Tests\Feature\Quotation;

use App\Core\Enums\LeadStatus;
use App\Domains\Quotation\Events\LeadCreated;
use App\Domains\Quotation\Events\LeadQualified;
use App\Domains\Quotation\Services\LeadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use RuntimeException;
use Tests\TestCase;

class LeadLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_fires_lead_created(): void
    {
        Event::fake([LeadCreated::class]);

        $lead = app(LeadService::class)->create([
            'full_name' => 'Jane Doe',
            'email' => 'jane-'.uniqid().'@example.com',
        ]);

        $this->assertSame(LeadStatus::New, $lead->status);
        Event::assertDispatched(LeadCreated::class);
    }

    public function test_full_lifecycle_transitions(): void
    {
        Event::fake([LeadQualified::class]);
        $leads = app(LeadService::class);

        $lead = $leads->create(['full_name' => 'John Doe', 'email' => 'john-'.uniqid().'@example.com']);

        $lead = $leads->contact($lead);
        $this->assertSame(LeadStatus::Contacted, $lead->status);

        $lead = $leads->qualify($lead);
        $this->assertSame(LeadStatus::Qualified, $lead->status);
        Event::assertDispatched(LeadQualified::class);

        $lead = $leads->convert($lead);
        $this->assertSame(LeadStatus::Converted, $lead->status);
    }

    public function test_disqualify_and_archive(): void
    {
        $leads = app(LeadService::class);
        $lead = $leads->create(['full_name' => 'Disqualify Me', 'email' => 'dq-'.uniqid().'@example.com']);

        $lead = $leads->disqualify($lead);
        $this->assertSame(LeadStatus::Unqualified, $lead->status);

        $lead = $leads->archive($lead);
        $this->assertSame(LeadStatus::Archived, $lead->status);
    }

    public function test_qualify_an_archived_lead_throws(): void
    {
        $leads = app(LeadService::class);
        $lead = $leads->create(['full_name' => 'Archived Lead', 'email' => 'arch-'.uniqid().'@example.com']);
        $lead = $leads->archive($lead);

        $this->expectException(RuntimeException::class);

        $leads->qualify($lead);
    }

    public function test_cannot_convert_an_unqualified_lead(): void
    {
        $leads = app(LeadService::class);
        $lead = $leads->create(['full_name' => 'Unqualified Lead', 'email' => 'unq-'.uniqid().'@example.com']);
        $lead = $leads->disqualify($lead);

        $this->expectException(RuntimeException::class);

        $leads->convert($lead);
    }
}
