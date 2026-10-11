<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('kra_classification_code')->nullable()->after('taxable');
            $table->string('kra_item_code')->nullable()->after('kra_classification_code');
            $table->timestamp('kra_synced_at')->nullable()->after('kra_item_code');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['kra_classification_code', 'kra_item_code', 'kra_synced_at']);
        });
    }
};
