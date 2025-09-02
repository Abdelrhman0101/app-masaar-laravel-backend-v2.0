<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'property_id',
        'customer_id',
        'provider_id',
        'appointment_datetime',
        'note',
        'admin_note',
        'provider_note',
        'status',
        'last_action_by',
        'updated_by',
    ];

    public function property()    { return $this->belongsTo(Property::class); }
    public function customer()    { return $this->belongsTo(User::class, 'customer_id'); }
    public function provider()    { return $this->belongsTo(User::class, 'provider_id'); }
    public function updater()     { return $this->belongsTo(User::class, 'updated_by'); }
}
