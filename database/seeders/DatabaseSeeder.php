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
        $now = Carbon::now();

        // ============================================================
        // 組織（B2Bテスト用）
        // ============================================================
        DB::table('organizations')->insert([
            'id'            => 1,
            'name'          => '前橋市営住宅管理センター',
            'contact_name'  => '田中 太郎',
            'contact_email' => 'tanaka@maebashi-housing.test',
            'contact_phone' => '027-000-0000',
            'address'       => '群馬県前橋市大手町1-1-1',
            'notes'         => 'テスト組織',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // ============================================================
        // 管理者ユーザー
        // ============================================================

        // マスター管理者（運営）
        DB::table('admin_users')->insert([
            'email'           => 'admin@mimamori.test',
            'password_hash'   => Hash::make('password'),
            'name'            => '管理者',
            'role'            => 'master',
            'organization_id' => null,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // 組織管理者（自治体）
        DB::table('admin_users')->insert([
            'email'           => 'tanaka@maebashi-housing.test',
            'password_hash'   => Hash::make('password'),
            'name'            => '田中 太郎',
            'role'            => 'operator',
            'organization_id' => 1,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // ============================================================
        // 個人デバイス（3台）
        // ============================================================

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
        // 組織デバイス（5台 - 前橋市営住宅）
        // ============================================================

        // 101号室: 正常
        DB::table('devices')->insert([
            'device_id'                => 'D2H5K8',
            'pin_hash'                 => Hash::make('1111'),
            'nickname'                 => null,
            'location_memo'            => '玄関ドア上',
            'status'                   => 'normal',
            'battery_voltage'          => 2.90,
            'battery_pct'              => 90,
            'rssi'                     => -68,
            'last_received_at'         => $now->copy()->subHours(2),
            'last_human_detected_at'   => $now->copy()->subHours(3),
            'alert_threshold_hours'    => 24,
            'pet_exclusion_enabled'    => 0,
            'pet_exclusion_threshold_cm' => 100,
            'install_height_cm'        => 200,
            'away_mode'                => 0,
            'away_until'               => null,
            'organization_id'          => 1,
            'activated_at'             => $now->copy()->subDays(60),
            'warranty_expires_at'      => $now->copy()->addYear()->toDateString(),
            'created_at'               => $now->copy()->subDays(60),
            'updated_at'               => $now,
        ]);

        // 102号室: 未検知アラート
        DB::table('devices')->insert([
            'device_id'                => 'E9J3M6',
            'pin_hash'                 => Hash::make('2222'),
            'nickname'                 => null,
            'location_memo'            => '玄関ドア上',
            'status'                   => 'alert',
            'battery_voltage'          => 2.75,
            'battery_pct'              => 70,
            'rssi'                     => -78,
            'last_received_at'         => $now->copy()->subHours(4),
            'last_human_detected_at'   => $now->copy()->subHours(28),
            'alert_threshold_hours'    => 24,
            'pet_exclusion_enabled'    => 0,
            'pet_exclusion_threshold_cm' => 100,
            'install_height_cm'        => 200,
            'away_mode'                => 0,
            'away_until'               => null,
            'organization_id'          => 1,
            'activated_at'             => $now->copy()->subDays(55),
            'warranty_expires_at'      => $now->copy()->addYear()->toDateString(),
            'created_at'               => $now->copy()->subDays(55),
            'updated_at'               => $now,
        ]);

        // 103号室: 通信途絶
        DB::table('devices')->insert([
            'device_id'                => 'F4L7N2',
            'pin_hash'                 => Hash::make('3333'),
            'nickname'                 => null,
            'location_memo'            => '玄関ドア上',
            'status'                   => 'offline',
            'battery_voltage'          => 2.30,
            'battery_pct'              => 20,
            'rssi'                     => -92,
            'last_received_at'         => $now->copy()->subDays(3),
            'last_human_detected_at'   => $now->copy()->subDays(3),
            'alert_threshold_hours'    => 24,
            'pet_exclusion_enabled'    => 0,
            'pet_exclusion_threshold_cm' => 100,
            'install_height_cm'        => 200,
            'away_mode'                => 0,
            'away_until'               => null,
            'organization_id'          => 1,
            'activated_at'             => $now->copy()->subDays(50),
            'warranty_expires_at'      => $now->copy()->addYear()->toDateString(),
            'created_at'               => $now->copy()->subDays(50),
            'updated_at'               => $now,
        ]);

        // 201号室: 正常
        DB::table('devices')->insert([
            'device_id'                => 'G8P1R5',
            'pin_hash'                 => Hash::make('4444'),
            'nickname'                 => null,
            'location_memo'            => '玄関ドア上',
            'status'                   => 'normal',
            'battery_voltage'          => 2.88,
            'battery_pct'              => 88,
            'rssi'                     => -71,
            'last_received_at'         => $now->copy()->subHours(1),
            'last_human_detected_at'   => $now->copy()->subHours(1),
            'alert_threshold_hours'    => 24,
            'pet_exclusion_enabled'    => 0,
            'pet_exclusion_threshold_cm' => 100,
            'install_height_cm'        => 200,
            'away_mode'                => 0,
            'away_until'               => null,
            'organization_id'          => 1,
            'activated_at'             => $now->copy()->subDays(45),
            'warranty_expires_at'      => $now->copy()->addYear()->toDateString(),
            'created_at'               => $now->copy()->subDays(45),
            'updated_at'               => $now,
        ]);

        // 202号室: 空室（未稼働）
        DB::table('devices')->insert([
            'device_id'                => 'H3S6T9',
            'pin_hash'                 => Hash::make('5555'),
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
            'organization_id'          => 1,
            'activated_at'             => null,
            'warranty_expires_at'      => $now->copy()->addYear()->toDateString(),
            'created_at'               => $now->copy()->subDays(40),
            'updated_at'               => $now,
        ]);

        // ============================================================
        // 組織デバイス割当
        // ============================================================
        $orgDeviceIds = DB::table('devices')
            ->where('organization_id', 1)
            ->pluck('id', 'device_id');

        $assignments = [
            ['device_id' => 'D2H5K8', 'room' => '101', 'tenant' => '佐藤 花子'],
            ['device_id' => 'E9J3M6', 'room' => '102', 'tenant' => '鈴木 一郎'],
            ['device_id' => 'F4L7N2', 'room' => '103', 'tenant' => '高橋 美咲'],
            ['device_id' => 'G8P1R5', 'room' => '201', 'tenant' => '渡辺 健太'],
            ['device_id' => 'H3S6T9', 'room' => '202', 'tenant' => null],
        ];

        foreach ($assignments as $a) {
            DB::table('org_device_assignments')->insert([
                'organization_id' => 1,
                'device_id'       => $orgDeviceIds[$a['device_id']],
                'room_number'     => $a['room'],
                'tenant_name'     => $a['tenant'],
                'assigned_at'     => now(),
            ]);
        }

        // ============================================================
        // 通知設定（個人デバイス1と2）
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
        $this->command->info('【マスター管理者ログイン】 /admin/login');
        $this->command->info('  Email:    admin@mimamori.test');
        $this->command->info('  Password: password');
        $this->command->info('  → /admin に遷移');
        $this->command->info('');
        $this->command->info('【組織管理者ログイン】 /admin/login');
        $this->command->info('  Email:    tanaka@maebashi-housing.test');
        $this->command->info('  Password: password');
        $this->command->info('  → /admin/org に遷移');
        $this->command->info('');
        $this->command->info('【デバイスログイン】 /login');
        $this->command->info('  ID: A3K9X2  PIN: 1234 (正常)');
        $this->command->info('  ID: B7M2P5  PIN: 5678 (アラート)');
        $this->command->info('  ID: C4N8Q1  PIN: 9012 (未稼働)');
        $this->command->info('');
    }
}
