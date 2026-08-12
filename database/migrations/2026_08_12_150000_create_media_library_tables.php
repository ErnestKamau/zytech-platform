<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_folders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('visibility')->default('public');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('media_folders', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('media_folders')->nullOnDelete();
        });

        Schema::create('media_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('media_media_tag', function (Blueprint $table) {
            $table->foreignUuid('media_id')->constrained('media')->cascadeOnDelete();
            $table->foreignUuid('media_tag_id')->constrained('media_tags')->cascadeOnDelete();
            $table->primary(['media_id', 'media_tag_id']);
        });

        Schema::create('media_usages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('media_id')->constrained('media')->cascadeOnDelete();
            $table->uuidMorphs('usable');
            $table->string('context')->default('default');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['media_id', 'usable_type', 'usable_id', 'context']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_usages');
        Schema::dropIfExists('media_media_tag');
        Schema::dropIfExists('media_tags');
        Schema::dropIfExists('media_folders');
    }
};
