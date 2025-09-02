<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealEstateOfficesDetail extends Model
{
    protected $fillable = [
        'real_estate_id',
        'office_name',
        'office_address',
        'office_phone',
        'logo_image',
        'owner_id_front_image',
        'owner_id_back_image',
        'office_image',
        'commercial_register_front_image',
        'commercial_register_back_image',
        'tax_enabled',
    ];

    public function realEstate()
    {
        return $this->belongsTo(RealEstate::class);
    }
}
