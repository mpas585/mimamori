<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['device_limit', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->unsignedSmallInteger('device_limit')->default(100)->after('notes')->comment('契約台数上限');
            $table->date('expires_at')->nullable()->after('device_limit')->comment('契約期限');
        });
    }
};
