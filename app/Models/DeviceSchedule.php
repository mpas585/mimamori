<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceSchedule extends Model
{
    protected $fillable = [
        'device_id',
        'type',
        'start_at',
        'end_at',
        'days_of_week',
        'start_time',
        'end_time',
        'next_day',
        'memo',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'days_of_week' => 'array',
            'next_day' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
