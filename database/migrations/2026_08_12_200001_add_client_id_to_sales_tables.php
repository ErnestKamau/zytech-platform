<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_leads', function (Blueprint $table) {
            $table->foreignUuid('client_id')->nullable()->after('id')->constrained('clients')->nullOnDelete();
        });

        Schema::table('quotation_requests', function (Blueprint $table) {
            $table->foreignUuid('client_id')->nullable()->after('sales_lead_id')->constrained('clients')->nullOnDelete();
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->foreignUuid('client_id')->nullable()->after('sales_lead_id')->constrained('clients')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
        });

        Schema::table('quotation_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
        });

        Schema::table('sales_leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
        });
    }
};
