<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('kra_pin')->nullable()->after('tax_number');
            $table->string('vat_number')->nullable()->after('kra_pin');
            $table->string('bank_name')->nullable()->after('vat_number');
            $table->string('bank_account_name')->nullable()->after('bank_name');
            $table->string('bank_account_number')->nullable()->after('bank_account_name');
            $table->string('bank_branch')->nullable()->after('bank_account_number');
            $table->string('mpesa_paybill')->nullable()->after('bank_branch');
            $table->string('mpesa_account_name')->nullable()->after('mpesa_paybill');
            $table->string('logo_path')->nullable()->after('mpesa_account_name');
            $table->string('address_line1')->nullable()->after('logo_path');
            $table->string('address_line2')->nullable()->after('address_line1');
            $table->string('address_city')->nullable()->after('address_line2');
            $table->string('address_county')->nullable()->after('address_city');
            $table->string('address_country')->default('Kenya')->after('address_county');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'kra_pin',
                'vat_number',
                'bank_name',
                'bank_account_name',
                'bank_account_number',
                'bank_branch',
                'mpesa_paybill',
                'mpesa_account_name',
                'logo_path',
                'address_line1',
                'address_line2',
                'address_city',
                'address_county',
                'address_country',
            ]);
        });
    }
};
