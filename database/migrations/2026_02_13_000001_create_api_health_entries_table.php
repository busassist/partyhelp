<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_health_entries', function (Blueprint $table) {
            $table->id();
            $table->string('service', 50)->index();
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_health_entries');
    }
};
