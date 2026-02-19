<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('notification_email_1', 255)->nullable()->after('contact_phone')->comment('通知メール1');
            $table->string('notification_email_2', 255)->nullable()->after('notification_email_1')->comment('通知メール2');
            $table->string('notification_email_3', 255)->nullable()->after('notification_email_2')->comment('通知メール3');
            $table->boolean('notification_enabled')->default(true)->after('notification_email_3')->comment('組織通知ON/OFF');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn([
                'notification_email_1',
                'notification_email_2',
                'notification_email_3',
                'notification_enabled',
            ]);
        });
    }
};
