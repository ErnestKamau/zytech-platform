<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_queries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('query');
            $table->string('context')->default('website');
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('result_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index('query');
        });

        Schema::create('seo_metadata', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('entity_type');
            $table->uuid('entity_id')->nullable();
            $table->string('path')->nullable()->unique();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_image')->nullable();
            $table->json('structured_data')->nullable();
            $table->unsignedTinyInteger('seo_score')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['entity_type', 'entity_id']);
        });

        Schema::create('seo_redirects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('from_path')->unique();
            $table->string('to_path');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // PostgreSQL full-text helpers on public catalogue tables
        DB::statement('CREATE INDEX IF NOT EXISTS projects_search_idx ON projects USING GIN (to_tsvector(\'english\', coalesce(title,\'\') || \' \' || coalesce(excerpt,\'\')))');
        DB::statement('CREATE INDEX IF NOT EXISTS services_search_idx ON services USING GIN (to_tsvector(\'english\', coalesce(title,\'\') || \' \' || coalesce(excerpt,\'\')))');
        DB::statement('CREATE INDEX IF NOT EXISTS articles_search_idx ON articles USING GIN (to_tsvector(\'english\', coalesce(title,\'\') || \' \' || coalesce(excerpt,\'\')))');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS projects_search_idx');
        DB::statement('DROP INDEX IF EXISTS services_search_idx');
        DB::statement('DROP INDEX IF EXISTS articles_search_idx');

        Schema::dropIfExists('seo_redirects');
        Schema::dropIfExists('seo_metadata');
        Schema::dropIfExists('search_queries');
    }
};
