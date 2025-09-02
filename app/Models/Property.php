<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'real_estate_id',
        'user_id',
        'address',
        'type',
        'price',
        'description',
        'image_url',
        'bedrooms',
        'bathrooms',
        'view',
        'payment_method',
        'area',
        'submitted_by',
        'submitted_price',
        'is_ready',
        'the_best',
    ];

    // علاقة العقار بمقدم الخدمة
    public function realEstate()
    {
        return $this->belongsTo(RealEstate::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
      public function user()
    {
        return $this->belongsTo(User::class);
    }

}
