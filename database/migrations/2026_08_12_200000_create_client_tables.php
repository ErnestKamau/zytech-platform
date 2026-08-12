<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('client_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('individual');
            $table->string('status')->default('prospect');
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('industry')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_key')->nullable();
            $table->string('photo_key')->nullable();
            $table->string('preferred_contact_method')->default('email');
            $table->text('summary')->nullable();
            $table->foreignUuid('assigned_sales_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('assigned_pm_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('portal_access_granted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('client_tag', function (Blueprint $table) {
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignUuid('client_tag_id')->constrained('client_tags')->cascadeOnDelete();
            $table->primary(['client_id', 'client_tag_id']);
        });

        Schema::create('client_group', function (Blueprint $table) {
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignUuid('client_group_id')->constrained('client_groups')->cascadeOnDelete();
            $table->primary(['client_id', 'client_group_id']);
        });

        Schema::create('client_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('name');
            $table->string('role')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('client_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->string('line1');
            $table->string('line2')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('country')->default('Kenya');
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('client_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('title');
            $table->string('kind')->default('general');
            $table->string('stored_path')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('visibility')->default('staff');
            $table->foreignUuid('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('client_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_internal')->default(true);
            $table->foreignUuid('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('client_timelines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('event_type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('occurred_at');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('client_communications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('channel');
            $table->string('subject')->nullable();
            $table->text('summary');
            $table->timestamp('occurred_at');
            $table->foreignUuid('logged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('client_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->unique()->constrained('clients')->cascadeOnDelete();
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('whatsapp_notifications')->default(true);
            $table->boolean('marketing_opt_in')->default(false);
            $table->timestamps();
        });

        Schema::create('client_status_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->foreignUuid('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('client_project', function (Blueprint $table) {
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->boolean('is_favorite')->default(false);
            $table->primary(['client_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_project');
        Schema::dropIfExists('client_status_history');
        Schema::dropIfExists('client_preferences');
        Schema::dropIfExists('client_communications');
        Schema::dropIfExists('client_timelines');
        Schema::dropIfExists('client_notes');
        Schema::dropIfExists('client_documents');
        Schema::dropIfExists('client_addresses');
        Schema::dropIfExists('client_contacts');
        Schema::dropIfExists('client_group');
        Schema::dropIfExists('client_tag');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('client_groups');
        Schema::dropIfExists('client_tags');
    }
};
