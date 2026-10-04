<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE services ALTER COLUMN gallery_keys TYPE jsonb USING gallery_keys::jsonb');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE services ALTER COLUMN gallery_keys TYPE json USING gallery_keys::json');
    }
};
