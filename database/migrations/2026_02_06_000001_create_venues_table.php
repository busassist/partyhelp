<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('business_name');
            $table->string('abn', 20)->nullable();
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->string('website')->nullable();
            $table->string('address');
            $table->string('suburb');
            $table->string('state')->default('VIC');
            $table->string('postcode', 10);
            $table->json('suburb_tags')->nullable();
            $table->json('occasion_tags')->nullable();
            $table->decimal('credit_balance', 10, 2)->default(0);
            $table->decimal('auto_topup_threshold', 10, 2)->default(75.00);
            $table->decimal('auto_topup_amount', 10, 2)->default(50.00);
            $table->boolean('auto_topup_enabled')->default(true);
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_payment_method_id')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive', 'suspended'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
