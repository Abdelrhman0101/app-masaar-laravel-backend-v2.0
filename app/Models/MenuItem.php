<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'restaurant_id',
        'section_id',
        'title',
        'description',
        'price',
        'image',
    ];

    // العلاقة: كل وجبة بتنتمي لقسم
    public function section()
    {
        return $this->belongsTo(MenuSection::class, 'section_id');
    }
}
