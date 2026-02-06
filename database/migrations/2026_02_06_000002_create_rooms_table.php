<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('style', ['bar', 'function_room', 'pub', 'club', 'semi_outdoor']);
            $table->integer('min_capacity')->default(10);
            $table->integer('max_capacity');
            $table->integer('seated_capacity')->nullable();
            $table->decimal('hire_cost_min', 10, 2)->nullable();
            $table->decimal('hire_cost_max', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->json('images')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
