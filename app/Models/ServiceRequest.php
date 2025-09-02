<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = [
        'user_id', 'governorate', 'type', 'status', 'request_data', 'approved_by_admin', 'selected_offer_id'
    ];

    protected $casts = [
        'request_data' => 'array',
        'approved_by_admin' => 'boolean',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function offers() {
        return $this->hasMany(Offer::class, 'service_request_id');
    }
}

