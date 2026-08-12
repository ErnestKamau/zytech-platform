<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Seeder;

class QuotationSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            ['name' => 'Website', 'slug' => 'website', 'description' => 'Public quotation form.', 'sort_order' => 0],
            ['name' => 'Phone', 'slug' => 'phone', 'description' => 'Inbound phone enquiries.', 'sort_order' => 1],
            ['name' => 'Referral', 'slug' => 'referral', 'description' => 'Client or partner referrals.', 'sort_order' => 2],
            ['name' => 'Walk-in', 'slug' => 'walk-in', 'description' => 'Office or site walk-ins.', 'sort_order' => 3],
        ];

        foreach ($sources as $source) {
            LeadSource::query()->updateOrCreate(
                ['slug' => $source['slug']],
                [...$source, 'is_active' => true],
            );
        }
    }
}
