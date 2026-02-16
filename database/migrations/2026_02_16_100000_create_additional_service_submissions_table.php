<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('additional_service_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->timestamp('submitted_at');
            $table->json('selected_service_ids')->nullable(); // array of additional_service ids
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('additional_service_submissions');
    }
};
