<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('symbol')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku')->nullable()->unique();
            $table->string('title');
            $table->json('attributes')->nullable();
            $table->decimal('price_amount', 12, 2)->nullable();
            $table->foreignUuid('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->integer('stock_display')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'is_active']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignUuid('brand_id')->nullable()->after('product_category_id')->constrained('brands')->nullOnDelete();
            $table->foreignUuid('default_unit_id')->nullable()->after('brand_id')->constrained('units')->nullOnDelete();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignUuid('product_variant_id')->nullable()->after('product_id')->constrained('product_variants')->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignUuid('product_variant_id')->nullable()->after('product_id')->constrained('product_variants')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_variant_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_variant_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_unit_id');
            $table->dropConstrainedForeignId('brand_id');
        });

        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('units');
        Schema::dropIfExists('brands');
    }
};
