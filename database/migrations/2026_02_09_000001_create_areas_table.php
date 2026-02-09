<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('area_postcode', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->foreignId('postcode_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['area_id', 'postcode_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_postcode');
        Schema::dropIfExists('areas');
    }
};
