<?php

namespace Tests\Feature\Company;

use App\Domains\Company\Services\CompanyService;
use App\Models\Company;
use App\Models\CompanyStatistic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyAdminStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_statistics_returns_visible_sorted_records(): void
    {
        $company = Company::query()->create([
            'name' => 'Zytech',
            'slug' => 'zytech',
            'status' => 'published',
        ]);

        CompanyStatistic::query()->create([
            'company_id' => $company->id,
            'label' => 'Projects',
            'value' => '120+',
            'is_visible' => true,
            'sort_order' => 2,
        ]);

        CompanyStatistic::query()->create([
            'company_id' => $company->id,
            'label' => 'Years',
            'value' => '10',
            'is_visible' => true,
            'sort_order' => 1,
        ]);

        CompanyStatistic::query()->create([
            'company_id' => $company->id,
            'label' => 'Hidden',
            'value' => '0',
            'is_visible' => false,
            'sort_order' => 0,
        ]);

        $stats = app(CompanyService::class)->adminStatistics();

        $this->assertCount(2, $stats);
        $this->assertSame('Years', $stats->first()->label);
        $this->assertSame('Projects', $stats->last()->label);
    }
}
