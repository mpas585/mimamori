<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('billing_contract_id');
            $table->unsignedInteger('amount')->comment('請求額');
            $table->unsignedSmallInteger('device_count')->comment('本体台数（課金時点）');
            $table->unsignedSmallInteger('premium_device_count')->comment('プレミアム台数（課金時点）');
            $table->string('payjp_charge_id', 255)->nullable()->comment('Pay.jp Charge ID');
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->text('error_message')->nullable();
            $table->dateTime('billed_at');
            $table->timestamps();

            $table->index('billing_contract_id');
            $table->index('status');
            $table->foreign('billing_contract_id')->references('id')->on('billing_contracts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_logs');
    }
};
