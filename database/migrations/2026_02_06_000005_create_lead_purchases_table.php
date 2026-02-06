<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_match_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_paid', 10, 2);
            $table->integer('discount_percent')->default(0);
            $table->enum('lead_status', [
                'contacted', 'quoted', 'booked', 'lost', 'pending',
            ])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['lead_id', 'venue_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_purchases');
    }
};
