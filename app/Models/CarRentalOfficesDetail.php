<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarRentalOfficesDetail extends Model
{
    protected $fillable = [
        'car_rental_id',
        'office_name',
        'logo_image',
        'commercial_register_front_image',
        'commercial_register_back_image',
        'payment_methods',
        'rental_options',
        'cost_per_km',
        'daily_driver_cost',
        'max_km_per_day',
        'is_available_for_delivery',   // جديد
        'is_available_for_rent',       // جديد
    ];
    protected $casts = [
        'payment_methods' => 'array',
        'rental_options' => 'array',
        'is_available_for_delivery' => 'boolean', // جديد
        'is_available_for_rent' => 'boolean',     // جديد
    ];

    public function carRental()
    {
        return $this->belongsTo(CarRental::class);
    }
}
