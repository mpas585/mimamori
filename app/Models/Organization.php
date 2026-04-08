<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address',
        'notes',
        'device_limit',
        'expires_at',
        'notification_email_1',
        'notification_email_2',
        'notification_email_3',
        'notification_enabled',
        'notification_sms_1',
        'notification_sms_2',
        'notification_sms_enabled',
        'premium_enabled',
        'delivery_name',
        'delivery_postal',
        'delivery_address',
        'delivery_phone',
    ];

    protected $casts = [
        'notification_enabled'     => 'boolean',
        'notification_sms_enabled' => 'boolean',
        'premium_enabled'          => 'boolean',
        'expires_at'               => 'date',
    ];

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function deviceAssignments(): HasMany
    {
        return $this->hasMany(OrgDeviceAssignment::class);
    }

    public function partnerUsers(): HasMany
    {
        return $this->hasMany(PartnerUser::class);
    }

    public function getNotificationEmails(): array
    {
        if (!$this->notification_enabled) {
            return [];
        }

        $emails = [];
        foreach (['notification_email_1', 'notification_email_2', 'notification_email_3'] as $field) {
            if (!empty($this->$field)) {
                $emails[] = $this->$field;
            }
        }

        return $emails;
    }

    public function getNotificationSmsPhones(): array
    {
        if (!$this->notification_sms_enabled) {
            return [];
        }

        $phones = [];
        foreach (['notification_sms_1', 'notification_sms_2'] as $field) {
            if (!empty($this->$field)) {
                $phones[] = $this->$field;
            }
        }

        return $phones;
    }
}
