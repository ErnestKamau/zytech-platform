<?php

namespace Tests\Feature\Quotation;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\ProjectStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\QuotationStatus;
use App\Domains\Project\Actions\ConvertQuotationToProject;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Quotation;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConvertQuotationToProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepted_quotation_can_be_converted_to_a_draft_project(): void
    {
        $quotation = $this->acceptedQuotation();
        $category = $this->category();

        $project = app(ConvertQuotationToProject::class)->handle($quotation, [
            'project_category_id' => $category->id,
            'title' => 'Westlands Residence Fit-out',
        ]);

        $this->assertSame('Westlands Residence Fit-out', $project->title);
        $this->assertSame(ProjectStatus::Draft, $project->status);
        $this->assertSame($category->id, $project->project_category_id);
        $this->assertSame($quotation->client->name, $project->client_name);
        $this->assertSame($project->id, $quotation->fresh()->converted_project_id);
    }

    public function test_conversion_defaults_project_type_when_quotation_has_no_originating_request(): void
    {
        $quotation = $this->acceptedQuotation();

        $project = app(ConvertQuotationToProject::class)->handle($quotation, [
            'project_category_id' => $this->category()->id,
        ]);

        $this->assertSame(ProjectType::Commercial, $project->type);
    }

    public function test_converting_the_same_quotation_twice_returns_the_same_project(): void
    {
        $quotation = $this->acceptedQuotation();
        $action = app(ConvertQuotationToProject::class);

        $first = $action->handle($quotation, ['project_category_id' => $this->category()->id]);
        $second = $action->handle($quotation->fresh(), ['project_category_id' => $this->category()->id]);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Project::query()->where('id', $first->id)->count());
    }

    public function test_a_quotation_that_is_not_accepted_cannot_be_converted(): void
    {
        $quotation = $this->acceptedQuotation();
        $quotation->forceFill(['status' => QuotationStatus::Sent])->save();

        $this->expectException(DomainException::class);

        app(ConvertQuotationToProject::class)->handle($quotation->fresh(), [
            'project_category_id' => $this->category()->id,
        ]);
    }

    private function category(): ProjectCategory
    {
        return ProjectCategory::query()->create([
            'name' => 'Residential',
            'slug' => 'residential-'.uniqid(),
            'is_published' => true,
            'sort_order' => 1,
        ]);
    }

    private function acceptedQuotation(): Quotation
    {
        $client = Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Project Conversion Client',
            'email' => 'project-conversion-'.uniqid().'@example.com',
        ]);

        return Quotation::query()->create([
            'reference_number' => 'ZQ-CONV-'.uniqid(),
            'client_id' => $client->id,
            'title' => 'Original quotation title',
            'status' => QuotationStatus::Accepted,
            'subtotal' => 100,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 100,
            'currency' => 'KES',
            'revision_number' => 1,
            'sent_at' => now(),
            'accepted_at' => now(),
        ])->fresh(['client']);
    }
}
