<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_run_logs', function (Blueprint $table) {
            $table->id();
            $table->string('task_key', 255)->index();
            $table->string('task_display_name', 255);
            $table->string('status', 20); // 'finished' | 'failed'
            $table->text('message')->nullable();
            $table->timestamp('ran_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_run_logs');
    }
};
