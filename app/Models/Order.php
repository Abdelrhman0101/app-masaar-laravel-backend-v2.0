<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'restaurant_id',
        'status',
        'order_number',
        'subtotal',
        'delivery_fee',
        'vat',
        'total_price',
        'note',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(RestaurantDetail::class, 'restaurant_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}

