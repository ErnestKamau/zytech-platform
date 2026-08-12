<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('subject');
            $table->string('status')->default('open');
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('portal_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('portal_conversation_id')->constrained('portal_conversations')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('portal_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('system');
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('portal_downloads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('client_document_id')->nullable()->constrained('client_documents')->nullOnDelete();
            $table->string('label');
            $table->string('stored_path')->nullable();
            $table->timestamp('downloaded_at');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('portal_favorites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->uuidMorphs('favoritable');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['client_id', 'favoritable_type', 'favoritable_id'], 'portal_favorites_unique');
        });

        Schema::create('portal_announcements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('body');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('reference_number')->unique();
            $table->string('subject');
            $table->text('body');
            $table->string('status')->default('open');
            $table->string('priority')->default('normal');
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('support_replies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('support_ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_staff')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('meeting_slots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('meeting_type')->default('consultation');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('meeting_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignUuid('meeting_slot_id')->nullable()->constrained('meeting_slots')->nullOnDelete();
            $table->string('meeting_type')->default('consultation');
            $table->string('status')->default('requested');
            $table->timestamp('scheduled_at')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_requests');
        Schema::dropIfExists('meeting_slots');
        Schema::dropIfExists('support_replies');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('portal_announcements');
        Schema::dropIfExists('portal_favorites');
        Schema::dropIfExists('portal_downloads');
        Schema::dropIfExists('portal_notifications');
        Schema::dropIfExists('portal_messages');
        Schema::dropIfExists('portal_conversations');
    }
};
