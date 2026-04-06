<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiCallLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'device_id',
        'call_sid',
        'recording_sid',
        'call_status',
        'judgment',
        'transcript',
        'gpt_response',
        'duration_sec',
        'error_message',
        'called_at',
    ];

    protected function casts(): array
    {
        return [
            'called_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}


