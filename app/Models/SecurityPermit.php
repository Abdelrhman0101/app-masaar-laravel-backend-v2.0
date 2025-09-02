<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityPermit extends Model
{
    protected $fillable = [
        'user_id',
        'travel_date',
        'nationality',
        'people_count',
        'coming_from',
        'passport_image',
        'other_document_image',
        'status',
        'notes',
    ];

    // علاقة التصريح بالمستخدم (صاحب الطلب)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
