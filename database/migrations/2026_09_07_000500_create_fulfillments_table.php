<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fulfillments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->nullable()->constrained('orders')->cascadeOnDelete();
            $table->foreignUuid('sales_order_id')->nullable()->constrained('sales_orders')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->string('method')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['order_id', 'status']);
            $table->index(['sales_order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fulfillments');
    }
};
