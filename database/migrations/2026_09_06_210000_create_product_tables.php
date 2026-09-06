<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('icon_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_category_id')->constrained('product_categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->text('excerpt')->nullable();
            $table->text('body')->nullable();
            $table->text('icon_path')->nullable();
            $table->string('image_key')->nullable();
            $table->json('gallery_keys')->nullable();
            $table->json('specifications')->nullable();
            $table->string('unit_of_measure')->nullable();
            $table->string('purchase_mode')->default('quote');
            $table->string('status')->default('draft');
            $table->string('visibility')->default('public');
            $table->string('pricing_model')->default('fixed');
            $table->decimal('price_amount', 12, 2)->nullable();
            $table->string('price_currency', 3)->default('KES');
            $table->string('price_unit')->nullable();
            $table->text('pricing_notes')->nullable();
            $table->boolean('taxable')->default(true);
            $table->integer('stock_display')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image_key')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quotation_request_product', function (Blueprint $table) {
            $table->foreignUuid('quotation_request_id')->constrained('quotation_requests')->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->primary(['quotation_request_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_request_product');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
    }
};
