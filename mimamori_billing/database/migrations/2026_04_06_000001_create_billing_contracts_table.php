<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable()->comment('組織（B2B）');
            $table->string('payjp_customer_id', 255)->nullable()->comment('Pay.jp Customer ID');
            $table->unsignedSmallInteger('device_count')->default(1)->comment('本体台数');
            $table->unsignedSmallInteger('premium_device_count')->default(0)->comment('プレミアム台数');
            $table->unsignedInteger('unit_price')->default(1000)->comment('本体単価（税込）');
            $table->unsignedInteger('premium_unit_price')->default(500)->comment('プレミアム単価（税込）');
            $table->unsignedInteger('amount')->default(0)->comment('今月の請求額（計算値）');
            $table->enum('status', ['active', 'canceled', 'past_due'])->default('active');
            $table->date('next_billing_date')->nullable()->comment('次回課金日');
            $table->dateTime('canceled_at')->nullable();
            $table->timestamps();

            $table->index('organization_id');
            $table->index('status');
            $table->index('next_billing_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_contracts');
    }
};
