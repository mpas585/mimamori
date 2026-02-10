<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceSimBinding extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'device_id',
        'iccid',
        'imei',
        'activated_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'activated_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
