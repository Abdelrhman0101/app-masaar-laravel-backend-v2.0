<?php
// app/Models/OrderOffer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderOffer extends Model
{
    protected $fillable = [
        'order_id',
        'offered_by',
        'price',
        'offer_note',
    ];

    public function order()
    {
        return $this->belongsTo(CarServiceOrder::class, 'order_id');
    }
}
