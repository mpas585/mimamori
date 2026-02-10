<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id')->comment('対象デバイス');
            $table->enum('type', ['alert', 'offline', 'battery_low', 'test', 'system']);
            $table->enum('channel', ['email', 'webpush', 'sms', 'voice']);
            $table->string('recipient', 255)->comment('送信先');
            $table->string('subject', 255)->nullable();
            $table->text('body')->nullable();
            $table->enum('status', ['sent', 'failed', 'pending'])->default('pending');
            $table->string('error_message', 500)->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['device_id', 'type']);
            $table->index('created_at');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
