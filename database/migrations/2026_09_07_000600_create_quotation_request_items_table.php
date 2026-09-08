<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_request_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quotation_request_id')->constrained('quotation_requests')->cascadeOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('description')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('unit_snapshot')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_request_items');
    }
};
