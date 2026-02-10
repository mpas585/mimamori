<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('watcher_device_id')->comment('見守る側');
            $table->unsignedBigInteger('target_device_id')->comment('見守られる側');
            $table->string('nickname', 50)->nullable()->comment('表示名（任意）');
            $table->boolean('notify_enabled')->default(true);
            $table->dateTime('approved_at')->nullable()->comment('承認日時');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['watcher_device_id', 'target_device_id'], 'uk_pair');
            $table->foreign('watcher_device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->foreign('target_device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchers');
    }
};
