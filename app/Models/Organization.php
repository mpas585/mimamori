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
    ];

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function deviceAssignments(): HasMany
    {
        return $this->hasMany(OrgDeviceAssignment::class);
    }
}
