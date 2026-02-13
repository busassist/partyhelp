<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bigquery_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('status', 20); // 'success' | 'failed'
            $table->text('message')->nullable();
            $table->text('error_detail')->nullable();
            $table->json('summary')->nullable(); // e.g. {"ph_leads": 100, "ph_venues": 50}
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bigquery_sync_logs');
    }
};
