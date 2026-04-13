<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TroubleReport extends Model
{
    protected $fillable = [
        'device_id',
        'type',
        'symptom',
        'description',
        'status',
        'admin_notes',
        'replacement_device_id',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function replacementDevice(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'replacement_device_id');
    }

    /**
     * 症状の選択肢
     */
    public static function symptomOptions(): array
    {
        return [
            'no_detection'     => 'センサーが反応しない',
            'false_detection'  => '誤検知が多い',
            'battery_drain'    => '電池の減りが早い',
            'no_communication' => '通信できない',
            'led_issue'        => 'LEDが点灯しない',
            'physical_damage'  => '本体の破損',
            'other'            => 'その他',
        ];
    }

    /**
     * ステータスの表示名
     */
    public static function statusLabels(): array
    {
        return [
            'open'        => '受付済み',
            'in_progress' => '対応中',
            'resolved'    => '解決済み',
            'closed'      => '完了',
        ];
    }

    /**
     * タイプの表示名
     */
    public static function typeLabels(): array
    {
        return [
            'malfunction'  => '故障・交換申請',
            'abuse_report' => '不正利用通報',
        ];
    }
}
