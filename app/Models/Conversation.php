<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بتعبئتها.
     */
    protected $fillable = [
        'user_id', // <-- الآن لدينا user_id فقط
        'status',
    ];

    /**
     * علاقة "واحد إلى كثير": المحادثة تحتوي على العديد من الرسائل.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * علاقة "تنتمي إلى": كل محادثة تنتمي إلى مستخدم واحد (غير المشرف).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}