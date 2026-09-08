<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            $table->string('warehouse_code')->nullable();
            $table->decimal('on_hand', 14, 2)->default(0);
            $table->decimal('reserved', 14, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['product_id', 'product_variant_id', 'warehouse_code'], 'inventory_levels_unique_combo');
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inventory_level_id')->constrained('inventory_levels')->cascadeOnDelete();
            $table->string('type');
            $table->decimal('quantity', 14, 2)->default(0);
            $table->string('reason')->nullable();
            $table->string('reference_type')->nullable();
            $table->uuid('reference_id')->nullable();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inventory_level_id')->constrained('inventory_levels')->cascadeOnDelete();
            $table->foreignUuid('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignUuid('sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->decimal('quantity', 14, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamp('reserved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['order_id', 'status']);
            $table->index(['sales_order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_reservations');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventory_levels');
    }
};
