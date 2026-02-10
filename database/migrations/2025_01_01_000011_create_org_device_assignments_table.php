<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('org_device_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('device_id');
            $table->string('room_number', 50)->nullable()->comment('部屋番号');
            $table->string('tenant_name', 100)->nullable()->comment('入居者名');
            $table->dateTime('assigned_at')->useCurrent();
            $table->dateTime('unassigned_at')->nullable();

            $table->unique(['organization_id', 'device_id'], 'uk_org_device');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('devices');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('org_device_assignments');
    }
};
