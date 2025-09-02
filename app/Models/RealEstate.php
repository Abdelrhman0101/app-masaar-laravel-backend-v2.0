<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealEstate extends Model
{
    protected $fillable = [
        'user_id',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function officeDetail()
    {
        return $this->hasOne(RealEstateOfficesDetail::class);
    }

    public function individualDetail()
    {
        return $this->hasOne(RealEstateIndividualsDetail::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

}
