<?php

use App\Core\Enums\UserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('admin_invite_token', 64)->nullable()->unique()->after('preferences');
            $table->timestamp('admin_invite_sent_at')->nullable()->after('admin_invite_token');
            $table->timestamp('admin_invite_expires_at')->nullable()->after('admin_invite_sent_at');
            $table->timestamp('admin_onboarded_at')->nullable()->after('admin_invite_expires_at');
        });

        DB::table('users')
            ->whereIn('type', [UserType::Administrator->value, UserType::Staff->value])
            ->whereNull('admin_onboarded_at')
            ->update(['admin_onboarded_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'admin_invite_token',
                'admin_invite_sent_at',
                'admin_invite_expires_at',
                'admin_onboarded_at',
            ]);
        });
    }
};
