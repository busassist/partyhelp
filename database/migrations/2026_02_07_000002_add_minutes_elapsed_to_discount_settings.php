<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discount_settings', function (Blueprint $table) {
            $table->unsignedSmallInteger('minutes_elapsed')->default(0)->after('hours_elapsed');
        });
    }

    public function down(): void
    {
        Schema::table('discount_settings', function (Blueprint $table) {
            $table->dropColumn('minutes_elapsed');
        });
    }
};
