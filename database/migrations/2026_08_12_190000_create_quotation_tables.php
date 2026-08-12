<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sales_leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lead_source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('status')->default('new');
            $table->string('priority')->default('normal');
            $table->text('notes')->nullable();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quotation_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_number')->unique();
            $table->foreignUuid('sales_lead_id')->nullable()->constrained('sales_leads')->nullOnDelete();
            $table->foreignUuid('lead_source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('project_type');
            $table->string('county')->nullable();
            $table->string('location')->nullable();
            $table->string('budget_range')->nullable();
            $table->string('estimated_timeline')->nullable();
            $table->text('description');
            $table->string('preferred_contact_method')->default('email');
            $table->string('status')->default('pending');
            $table->text('internal_notes')->nullable();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quotation_request_service', function (Blueprint $table) {
            $table->foreignUuid('quotation_request_id')->constrained('quotation_requests')->cascadeOnDelete();
            $table->foreignUuid('service_id')->constrained('services')->cascadeOnDelete();
            $table->primary(['quotation_request_id', 'service_id']);
        });

        Schema::create('quotation_request_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quotation_request_id')->constrained('quotation_requests')->cascadeOnDelete();
            $table->string('original_name');
            $table->string('stored_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('site_visits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quotation_request_id')->nullable()->constrained('quotation_requests')->nullOnDelete();
            $table->foreignUuid('sales_lead_id')->nullable()->constrained('sales_leads')->nullOnDelete();
            $table->string('visit_type')->default('site');
            $table->string('status')->default('scheduled');
            $table->timestamp('scheduled_at')->nullable();
            $table->string('location')->nullable();
            $table->string('engineer_name')->nullable();
            $table->text('notes')->nullable();
            $table->text('recommendations')->nullable();
            $table->foreignUuid('scheduled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_number')->unique();
            $table->foreignUuid('quotation_request_id')->nullable()->constrained('quotation_requests')->nullOnDelete();
            $table->foreignUuid('sales_lead_id')->nullable()->constrained('sales_leads')->nullOnDelete();
            $table->string('title');
            $table->string('type')->default('standard');
            $table->string('status')->default('draft');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->string('currency', 3)->default('KES');
            $table->date('valid_until')->nullable();
            $table->unsignedSmallInteger('revision_number')->default(1);
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->foreignUuid('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignUuid('converted_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quotation_sections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->foreignUuid('quotation_section_id')->nullable()->constrained('quotation_sections')->nullOnDelete();
            $table->string('label');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2)->default(0);
            $table->boolean('is_optional')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quotation_revisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->unsignedSmallInteger('revision_number');
            $table->string('status')->default('draft');
            $table->text('summary')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quotation_status_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quotation_request_id')->nullable()->constrained('quotation_requests')->cascadeOnDelete();
            $table->foreignUuid('quotation_id')->nullable()->constrained('quotations')->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->foreignUuid('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('quotation_approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->foreignUuid('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quotation_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->string('title');
            $table->string('kind')->default('pdf');
            $table->string('stored_path')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('verification_code')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_documents');
        Schema::dropIfExists('quotation_approvals');
        Schema::dropIfExists('quotation_status_history');
        Schema::dropIfExists('quotation_revisions');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotation_sections');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('site_visits');
        Schema::dropIfExists('quotation_request_attachments');
        Schema::dropIfExists('quotation_request_service');
        Schema::dropIfExists('quotation_requests');
        Schema::dropIfExists('sales_leads');
        Schema::dropIfExists('lead_sources');
    }
};
