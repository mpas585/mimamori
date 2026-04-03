<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_call_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->string('call_sid', 50)->nullable()->comment('Twilio CallSID');
            $table->string('recording_sid', 50)->nullable()->comment('Twilio RecordingSID');
            $table->enum('call_status', ['no_answer', 'completed', 'failed'])->default('failed');
            $table->enum('judgment', ['good', 'check', 'alert', 'unclear'])->nullable()->comment('AIによる判定');
            $table->text('transcript')->nullable()->comment('文字起こし');
            $table->text('gpt_response')->nullable()->comment('GPTの判定根拠');
            $table->unsignedSmallInteger('duration_sec')->nullable()->comment('通話時間（秒）');
            $table->string('error_message', 500)->nullable();
            $table->timestamp('called_at')->useCurrent()->comment('発信日時');

            $table->index(['device_id', 'called_at']);
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_call_logs');
    }
};
