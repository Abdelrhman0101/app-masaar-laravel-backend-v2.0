<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarRental extends Model
{
    protected $fillable = [
        'rental_type',
        'user_id', // لو بتستخدمه في الـ create أو أي حقول أخرى هتسجلها مباشرة
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function officeDetail()
    {
        return $this->hasOne(CarRentalOfficesDetail::class);
    }

    public function driverDetail()
    {
        return $this->hasOne(DriverDetail::class);
    }
    public function cars()
    {
        return $this->hasMany(Car::class);
    }
    public function carServiceOrders()
    {
        return $this->hasMany(CarServiceOrder::class);
    }

}
