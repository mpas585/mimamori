<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'device_id',
        'email_1',
        'email_2',
        'email_3',
        'email_enabled',
        'webpush_enabled',
        'webpush_subscription',
        'sms_phone_1',
        'sms_phone_2',
        'sms_enabled',
        'voice_phone_1',
        'voice_phone_2',
        'voice_enabled',
    ];

    protected function casts(): array
    {
        return [
            'email_enabled' => 'boolean',
            'webpush_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'voice_enabled' => 'boolean',
            'webpush_subscription' => 'array',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
