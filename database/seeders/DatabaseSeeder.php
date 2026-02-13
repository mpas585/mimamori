<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ============================================================
        // 管理者ユーザー
        // ============================================================
        DB::table('admin_users')->insert([
            'email'         => 'admin@mimamori.test',
            'password_hash' => Hash::make('password'),
            'name'          => '管理者',
            'role'          => 'master',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // ============================================================
        // テストデバイス（3台）
        // ============================================================
        $now = Carbon::now();

        // デバイス1: 正常稼働中
        DB::table('devices')->insert([
            'device_id'                => 'A3K9X2',
            'pin_hash'                 => Hash::make('1234'),
            'nickname'                 => 'おばあちゃん家',
            'location_memo'            => '玄関ドア上',
            'status'                   => 'normal',
            'battery_voltage'          => 2.85,
            'battery_pct'              => 85,
            'rssi'                     => -75,
            'last_received_at'         => $now->copy()->subHours(1),
            'last_human_detected_at'   => $now->copy()->subHours(2),
            'alert_threshold_hours'    => 24,
            'pet_exclusion_enabled'    => 0,
            'pet_exclusion_threshold_cm' => 100,
            'install_height_cm'        => 200,
            'away_mode'                => 0,
            'away_until'               => null,
            'organization_id'          => null,
            'activated_at'             => $now->copy()->subDays(30),
            'warranty_expires_at'      => $now->copy()->addYear()->toDateString(),
            'created_at'               => $now->copy()->subDays(30),
            'updated_at'               => $now,
        ]);

        // デバイス2: 未検知アラート状態
        DB::table('devices')->insert([
            'device_id'                => 'B7M2P5',
            'pin_hash'                 => Hash::make('5678'),
            'nickname'                 => 'おじいちゃん家',
            'location_memo'            => 'リビングドア上',
            'status'                   => 'alert',
            'battery_voltage'          => 2.70,
            'battery_pct'              => 65,
            'rssi'                     => -82,
            'last_received_at'         => $now->copy()->subHours(6),
            'last_human_detected_at'   => $now->copy()->subHours(30),
            'alert_threshold_hours'    => 24,
            'pet_exclusion_enabled'    => 1,
            'pet_exclusion_threshold_cm' => 100,
            'install_height_cm'        => 195,
            'away_mode'                => 0,
            'away_until'               => null,
            'organization_id'          => null,
            'activated_at'             => $now->copy()->subDays(20),
            'warranty_expires_at'      => $now->copy()->addYear()->toDateString(),
            'created_at'               => $now->copy()->subDays(20),
            'updated_at'               => $now,
        ]);

        // デバイス3: 未稼働
        DB::table('devices')->insert([
            'device_id'                => 'C4N8Q1',
            'pin_hash'                 => Hash::make('9012'),
            'nickname'                 => null,
            'location_memo'            => null,
            'status'                   => 'inactive',
            'battery_voltage'          => null,
            'battery_pct'              => null,
            'rssi'                     => null,
            'last_received_at'         => null,
            'last_human_detected_at'   => null,
            'alert_threshold_hours'    => 24,
            'pet_exclusion_enabled'    => 0,
            'pet_exclusion_threshold_cm' => 100,
            'install_height_cm'        => 200,
            'away_mode'                => 0,
            'away_until'               => null,
            'organization_id'          => null,
            'activated_at'             => null,
            'warranty_expires_at'      => $now->copy()->addYear()->toDateString(),
            'created_at'               => $now,
            'updated_at'               => $now,
        ]);

        // ============================================================
        // 通知設定（デバイス1と2）
        // ============================================================
        DB::table('notification_settings')->insert([
            'device_id'     => 1,
            'email_1'       => 'family@example.com',
            'email_2'       => null,
            'email_3'       => null,
            'email_enabled' => 1,
            'webpush_enabled'      => 0,
            'webpush_subscription' => null,
            'sms_phone_1'   => null,
            'sms_phone_2'   => null,
            'sms_enabled'   => 0,
            'voice_phone_1' => null,
            'voice_phone_2' => null,
            'voice_enabled' => 0,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('notification_settings')->insert([
            'device_id'     => 2,
            'email_1'       => 'family2@example.com',
            'email_2'       => null,
            'email_3'       => null,
            'email_enabled' => 1,
            'webpush_enabled'      => 0,
            'webpush_subscription' => null,
            'sms_phone_1'   => null,
            'sms_phone_2'   => null,
            'sms_enabled'   => 0,
            'voice_phone_1' => null,
            'voice_phone_2' => null,
            'voice_enabled' => 0,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // ============================================================
        // 検知ログ（デバイス1: 直近3日分）
        // ============================================================
        for ($i = 5; $i >= 0; $i--) {
            $periodEnd = $now->copy()->subHours($i * 12);
            $periodStart = $periodEnd->copy()->subHours(12);

            DB::table('detection_logs')->insert([
                'device_id'       => 1,
                'period_start'    => $periodStart,
                'period_end'      => $periodEnd,
                'detection_count' => rand(3, 15),
                'human_count'     => rand(2, 10),
                'pet_count'       => rand(0, 3),
                'last_distance_cm' => rand(25, 45),
                'battery_voltage' => 2.85,
                'battery_pct'     => 85 - $i,
                'rssi'            => rand(-80, -70),
                'error_code'      => null,
                'raw_json'        => null,
                'received_at'     => $periodEnd,
            ]);
        }

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('  テストデータ投入完了');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('【管理者ログイン】 /admin/login');
        $this->command->info('  Email:    admin@mimamori.test');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('【デバイスログイン】 /login');
        $this->command->info('  ID: A3K9X2  PIN: 1234 (正常)');
        $this->command->info('  ID: B7M2P5  PIN: 5678 (アラート)');
        $this->command->info('  ID: C4N8Q1  PIN: 9012 (未稼働)');
        $this->command->info('');
    }
}
