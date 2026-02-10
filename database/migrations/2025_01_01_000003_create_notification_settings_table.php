<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');

            // メール（無料・最大3件）
            $table->string('email_1', 255)->nullable();
            $table->string('email_2', 255)->nullable();
            $table->string('email_3', 255)->nullable();
            $table->boolean('email_enabled')->default(true);

            // Webプッシュ（無料）
            $table->boolean('webpush_enabled')->default(false);
            $table->json('webpush_subscription')->nullable()->comment('FCMサブスクリプション');

            // SMS（プレミアム）
            $table->string('sms_phone_1', 20)->nullable();
            $table->string('sms_phone_2', 20)->nullable();
            $table->boolean('sms_enabled')->default(false);

            // 自動音声電話（プレミアム）
            $table->string('voice_phone_1', 20)->nullable();
            $table->string('voice_phone_2', 20)->nullable();
            $table->boolean('voice_enabled')->default(false);

            $table->timestamps();

            $table->unique('device_id');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
