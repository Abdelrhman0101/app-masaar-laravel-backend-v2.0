<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverDetail extends Model
{
    protected $fillable = [
        'car_rental_id',
        'profile_image',
        'payment_methods',
        'rental_options',
        'cost_per_km',
        'daily_driver_cost',
        'max_km_per_day',
    ];
    protected $casts = [
        'payment_methods' => 'array',
        'rental_options' => 'array',
    ];

    public function carRental()
    {
        return $this->belongsTo(CarRental::class);
    }
}
