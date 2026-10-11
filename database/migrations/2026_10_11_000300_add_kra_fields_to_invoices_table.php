<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('type')->default('invoice')->after('status');
            $table->foreignUuid('parent_invoice_id')->nullable()->after('type')->constrained('invoices')->nullOnDelete();
            $table->text('kra_request_payload')->nullable();
            $table->text('kra_response_payload')->nullable();
            $table->string('kra_rcpt_no')->nullable();
            $table->string('kra_sdc_id')->nullable();
            $table->string('kra_invc_no')->nullable();
            $table->string('kra_sync_status')->default('not_synced');
            $table->text('kra_last_error')->nullable();
            $table->timestamp('kra_synced_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_invoice_id');
            $table->dropColumn([
                'type',
                'kra_request_payload',
                'kra_response_payload',
                'kra_rcpt_no',
                'kra_sdc_id',
                'kra_invc_no',
                'kra_sync_status',
                'kra_last_error',
                'kra_synced_at',
            ]);
        });
    }
};
