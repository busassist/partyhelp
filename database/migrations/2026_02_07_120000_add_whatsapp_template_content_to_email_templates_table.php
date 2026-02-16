<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->text('whatsapp_body')->nullable()->after('send_via_whatsapp');
            $table->string('whatsapp_accept_label', 50)->nullable()->after('whatsapp_body');
            $table->string('whatsapp_ignore_label', 50)->nullable()->after('whatsapp_accept_label');
            $table->string('twilio_content_sid', 100)->nullable()->after('whatsapp_ignore_label');
        });
    }

    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_body',
                'whatsapp_accept_label',
                'whatsapp_ignore_label',
                'twilio_content_sid',
            ]);
        });
    }
};
