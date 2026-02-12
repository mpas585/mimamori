<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pin_reset_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->string('token', 64)->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expires_at');

            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pin_reset_tokens');
    }
};
