<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_list_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('price_list_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 14, 2);
            $table->decimal('min_quantity', 12, 2)->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['price_list_id', 'product_id', 'min_quantity'], 'price_list_items_list_product_qty_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_list_items');
    }
};
