<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_sim_bindings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->string('iccid', 22)->comment('SIM ICCID');
            $table->string('imei', 20)->nullable()->comment('モジュールIMEI');
            $table->dateTime('activated_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique('device_id');
            $table->unique('iccid');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_sim_bindings');
    }
};
