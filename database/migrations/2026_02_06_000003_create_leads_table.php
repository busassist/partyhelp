<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('occasion_type');
            $table->integer('guest_count');
            $table->date('preferred_date');
            $table->string('suburb');
            $table->json('room_styles');
            $table->string('budget_range')->nullable();
            $table->text('special_requirements')->nullable();
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('current_price', 10, 2)->default(0);
            $table->integer('discount_percent')->default(0);
            $table->enum('status', [
                'new', 'distributed', 'partially_fulfilled',
                'fulfilled', 'expired', 'cancelled',
            ])->default('new');
            $table->integer('purchase_target')->default(3);
            $table->integer('purchase_count')->default(0);
            $table->timestamp('distributed_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('webhook_payload')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
