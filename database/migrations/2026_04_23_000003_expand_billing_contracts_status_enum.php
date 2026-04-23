<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * billing_contracts.status enum に以下を追加：
     *   - 'pending'   : 3Dセキュア認証中など未確定状態（PlanController/BillingControllerで既に使用中）
     *   - 'suspended' : 30日猶予経過後のデバイス停止状態
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE billing_contracts MODIFY COLUMN status ENUM('active', 'canceled', 'past_due', 'pending', 'suspended') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        // suspended / pending 状態のレコードは past_due に寄せる（downで値消失を防ぐ）
        DB::statement("UPDATE billing_contracts SET status = 'past_due' WHERE status IN ('suspended', 'pending')");
        DB::statement("ALTER TABLE billing_contracts MODIFY COLUMN status ENUM('active', 'canceled', 'past_due') NOT NULL DEFAULT 'active'");
    }
};
