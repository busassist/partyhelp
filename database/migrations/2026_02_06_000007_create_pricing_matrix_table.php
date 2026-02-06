<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_matrix', function (Blueprint $table) {
            $table->id();
            $table->string('occasion_type');
            $table->integer('guest_min');
            $table->integer('guest_max');
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['occasion_type', 'guest_min', 'guest_max']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_matrix');
    }
};
