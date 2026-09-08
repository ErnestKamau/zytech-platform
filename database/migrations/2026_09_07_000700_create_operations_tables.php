<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->nullable()->constrained('clients')->cascadeOnDelete();
            $table->foreignUuid('quotation_request_id')->nullable()->constrained('quotation_requests')->nullOnDelete();
            $table->foreignUuid('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignUuid('sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('due_at')->nullable();
            $table->string('status')->default('open');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['assigned_to', 'status']);
            $table->index(['client_id', 'status']);
        });

        // Immutable log: created_at only, no updated_at, no soft deletes.
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('subject_type')->nullable();
            $table->uuid('subject_id')->nullable();
            $table->foreignUuid('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event');
            $table->json('properties')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['subject_type', 'subject_id']);
        });

        Schema::create('assignment_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('subject_type');
            $table->uuid('subject_id');
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('unassigned_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_histories');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('follow_ups');
    }
};
