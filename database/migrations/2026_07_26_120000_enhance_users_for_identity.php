<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('type')->default('client')->after('email');
            $table->string('phone')->nullable()->after('type');
            $table->unsignedTinyInteger('failed_login_attempts')->default(0)->after('remember_token');
            $table->timestamp('locked_at')->nullable()->after('failed_login_attempts');
            $table->string('lock_reason')->nullable()->after('locked_at');
            $table->boolean('mfa_enabled')->default(false)->after('lock_reason');
            $table->text('mfa_secret')->nullable()->after('mfa_enabled');
            $table->json('preferences')->nullable()->after('mfa_secret');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'type',
                'phone',
                'failed_login_attempts',
                'locked_at',
                'lock_reason',
                'mfa_enabled',
                'mfa_secret',
                'preferences',
            ]);
        });
    }
};
