<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // orders（注文管理）
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique()->comment('注文番号');
            $table->string('customer_name', 100);
            $table->string('customer_email', 255);
            $table->string('customer_phone', 20)->nullable();
            $table->text('customer_address')->nullable();
            $table->smallInteger('quantity')->unsigned()->default(1);
            $table->unsignedInteger('unit_price')->comment('単価（税込）');
            $table->unsignedInteger('total_price')->comment('合計（税込）');
            $table->enum('payment_method', ['stripe', 'bank_transfer']);
            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            $table->string('stripe_payment_intent_id', 255)->nullable();
            $table->enum('shipping_status', ['pending', 'shipped', 'delivered'])->default('pending');
            $table->dateTime('shipped_at')->nullable();
            $table->string('tracking_number', 50)->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('precheck_agreed_at')->comment('購入前チェック同意日時');
            $table->timestamps();

            $table->index('payment_status');
            $table->index('customer_email');
        });

        // order_devices（注文-デバイス紐付け）
        Schema::create('order_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('device_id');
            $table->string('initial_pin', 4)->comment('初期PIN（平文、納品書用）');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('devices');
        });

        // precheck_agreements（購入前同意ログ）
        Schema::create('precheck_agreements', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100);
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->json('agreed_items')->comment('同意した7項目');
            $table->timestamp('agreed_at')->useCurrent();

            $table->index('session_id');
        });

        // trouble_reports（故障・通報）
        Schema::create('trouble_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->enum('type', ['malfunction', 'abuse_report'])->comment('故障 or 不正利用通報');
            $table->string('symptom', 100)->nullable()->comment('症状選択');
            $table->text('description')->nullable()->comment('詳細説明');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('replacement_device_id')->nullable()->comment('代替デバイスID');
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->foreign('device_id')->references('id')->on('devices');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trouble_reports');
        Schema::dropIfExists('precheck_agreements');
        Schema::dropIfExists('order_devices');
        Schema::dropIfExists('orders');
    }
};
