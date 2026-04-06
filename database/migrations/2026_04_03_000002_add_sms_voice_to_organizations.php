<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('notification_sms_1', 20)->nullable()->after('notification_email_3');
            $table->string('notification_sms_2', 20)->nullable()->after('notification_sms_1');
            $table->boolean('notification_sms_enabled')->default(false)->after('notification_sms_2');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['notification_sms_1', 'notification_sms_2', 'notification_sms_enabled']);
        });
    }
};


