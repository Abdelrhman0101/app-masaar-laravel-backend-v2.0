<?php
// app/Models/CarServiceOrder.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarServiceOrder extends Model
{
    protected $fillable = [
        'client_id',
        'car_rental_id',
        'provider_id',
        'order_type',
        'car_category',
        'payment_method',
        'status',
        'requested_price',
        'agreed_price',
        'from_location',
        'to_location',
        'delivery_time',
        'requested_date',
        'provider_offer_date',
        'accepted_at',
        'rejected_at',
    ];

    // العلاقات
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function carRental()
    {
        return $this->belongsTo(CarRental::class);
    }

    public function offers()
    {
        return $this->hasMany(OrderOffer::class, 'order_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id');
    }
}
