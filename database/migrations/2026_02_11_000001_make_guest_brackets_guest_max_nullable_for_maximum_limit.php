<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Make guest_max nullable so null means "maximum limit" (unlimited).
     * Backfill: brackets with guest_max >= 500 become 100+ (guest_min=100, guest_max=null).
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE guest_brackets MODIFY guest_max SMALLINT UNSIGNED NULL');
        } else {
            DB::statement('ALTER TABLE guest_brackets ALTER COLUMN guest_max DROP NOT NULL');
        }

        DB::table('guest_brackets')
            ->where('guest_max', '>=', 500)
            ->update(['guest_min' => 100, 'guest_max' => null]);
    }

    public function down(): void
    {
        DB::table('guest_brackets')
            ->whereNull('guest_max')
            ->update(['guest_max' => 500]);

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE guest_brackets MODIFY guest_max SMALLINT UNSIGNED NOT NULL');
        } else {
            DB::statement('ALTER TABLE guest_brackets ALTER COLUMN guest_max SET NOT NULL');
        }
    }
};
