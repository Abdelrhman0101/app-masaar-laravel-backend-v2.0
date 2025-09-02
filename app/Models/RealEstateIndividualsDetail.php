<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealEstateIndividualsDetail extends Model
{
    protected $fillable = [
        'real_estate_id',
        'profile_image',
        'agent_name',
        'agent_id_front_image',
        'agent_id_back_image',
        'tax_card_front_image',
        'tax_card_back_image',
    ];

    public function realEstate()
    {
        return $this->belongsTo(RealEstate::class);
    }
}
