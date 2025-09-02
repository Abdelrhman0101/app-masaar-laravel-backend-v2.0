<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'car_rental_id',
        'owner_type',
        'license_front_image',
        'license_back_image',
        'car_license_front',
        'car_license_back',
        'car_image_front',
        'car_image_back',
        'car_type',
        'car_model',
        'car_color',
        'price',
        'car_plate_number',
        'is_reviewed',
    ];

    public function carRental()
    {
        return $this->belongsTo(CarRental::class);
    }
}
