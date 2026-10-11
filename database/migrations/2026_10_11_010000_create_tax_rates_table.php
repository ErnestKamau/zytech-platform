<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->decimal('rate', 5, 2);
            $table->string('kra_tax_type_code', 1)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Seed rows matching today's effective behaviour (PricingService::DEFAULT_TAX_RATE)
        // so nothing changes for existing quotations until an admin edits these.
        $now = now();

        DB::table('tax_rates')->insert([
            [
                'id' => (string) Str::uuid(),
                'code' => 'VAT16',
                'name' => 'Standard VAT',
                'rate' => 16.00,
                'kra_tax_type_code' => 'B',
                'is_default' => true,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => (string) Str::uuid(),
                'code' => 'ZERO',
                'name' => 'Zero-rated',
                'rate' => 0.00,
                'kra_tax_type_code' => 'D',
                'is_default' => false,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
