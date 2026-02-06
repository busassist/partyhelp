<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->decimal('match_score', 5, 2)->default(0);
            $table->enum('status', [
                'notified', 'viewed', 'purchased', 'expired', 'declined',
            ])->default('notified');
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();

            $table->unique(['lead_id', 'venue_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_matches');
    }
};
