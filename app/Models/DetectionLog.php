<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetectionLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'device_id',
        'period_start',
        'period_end',
        'detection_count',
        'human_count',
        'pet_count',
        'last_distance_cm',
        'battery_voltage',
        'battery_pct',
        'rssi',
        'error_code',
        'raw_json',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'datetime',
            'period_end' => 'datetime',
            'received_at' => 'datetime',
            'raw_json' => 'array',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
