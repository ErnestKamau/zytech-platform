<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('location');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['location', 'is_published']);
        });

        Schema::create('navigation_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('navigation_menu_id')->constrained('navigation_menus')->cascadeOnDelete();
            $table->uuid('parent_id')->nullable();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();
            $table->string('target')->default('_self');
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('navigation_items', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('id')
                ->on('navigation_items')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('navigation_menus');
    }
};
