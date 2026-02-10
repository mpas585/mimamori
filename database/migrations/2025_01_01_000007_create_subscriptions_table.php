<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->enum('plan', ['free', 'premium'])->default('free');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->nullable()->comment('monthly=¥500/月, yearly=¥3000/年');
            $table->string('stripe_customer_id', 255)->nullable();
            $table->string('stripe_subscription_id', 255)->nullable();
            $table->date('current_period_start')->nullable();
            $table->date('current_period_end')->nullable();
            $table->enum('status', ['active', 'canceled', 'past_due', 'trialing'])->default('active');
            $table->dateTime('canceled_at')->nullable();
            $table->timestamps();

            $table->unique('device_id');
            $table->index('stripe_customer_id');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
