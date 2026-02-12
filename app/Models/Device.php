<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Device extends Authenticatable
{
    protected $fillable = [
        'device_id',
        'pin_hash',
        'nickname',
        'location_memo',
        'status',
        'battery_voltage',
        'battery_pct',
        'rssi',
        'last_received_at',
        'last_human_detected_at',
        'alert_threshold_hours',
        'pet_exclusion_enabled',
        'pet_exclusion_threshold_cm',
        'install_height_cm',
        'away_mode',
        'away_until',
        'organization_id',
        'activated_at',
        'warranty_expires_at',
    ];

    protected $hidden = [
        'pin_hash',
    ];

    protected function casts(): array
    {
        return [
            'last_received_at' => 'datetime',
            'last_human_detected_at' => 'datetime',
            'activated_at' => 'datetime',
            'away_until' => 'datetime',
            'warranty_expires_at' => 'date',
            'pet_exclusion_enabled' => 'boolean',
            'away_mode' => 'boolean',
        ];
    }

    /**
     * 認証用：PIN（パスワード）カラム名
     */
    public function getAuthPassword(): string
    {
        return $this->pin_hash;
    }

    // --- リレーション ---

    public function detectionLogs(): HasMany
    {
        return $this->hasMany(DetectionLog::class);
    }

    public function notificationSetting(): HasOne
    {
        return $this->hasOne(NotificationSetting::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function simBinding(): HasOne
    {
        return $this->hasOne(DeviceSimBinding::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * このデバイスが見守っている相手
     */
    public function watchingTargets(): HasMany
    {
        return $this->hasMany(Watcher::class, 'watcher_device_id');
    }

    /**
     * このデバイスを見守っている人
     */
    public function watchedBy(): HasMany
    {
        return $this->hasMany(Watcher::class, 'target_device_id');
    }

    /**
     * スケジュール（単発・定期）
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(DeviceSchedule::class);
    }
}
