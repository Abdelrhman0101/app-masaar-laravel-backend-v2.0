<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * الحقول المسموح بتعبئتها
     */
    protected $fillable = [
        'user1_id',
        'user2_id', 
        'status',
        'type',
        'title',
        'last_message_at',
        'metadata'
    ];

    /**
     * تحويل الحقول إلى أنواع البيانات المناسبة
     */
    protected $casts = [
        'last_message_at' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * القيم الافتراضية للحقول
     */
    protected $attributes = [
        'status' => 'open',
        'type' => 'private'
    ];

    /**
     * أنواع المحادثات المسموحة
     */
    const CONVERSATION_TYPES = [
        'private' => 'محادثة خاصة',
        'support' => 'دعم فني',
        'service' => 'خدمة',
        'admin' => 'إدارية'
    ];

    /**
     * حالات المحادثة المسموحة
     */
    const STATUSES = [
        'open' => 'مفتوحة',
        'closed' => 'مغلقة',
        'archived' => 'مؤرشفة'
    ];

    /**
     * علاقة المحادثة بالرسائل
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * علاقة المحادثة بالمستخدم الأول
     */
    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * علاقة المحادثة بالمستخدم الثاني
     */
    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * الحصول على جميع المشاركين في المحادثة
     */
    public function participants()
    {
        return collect([$this->user1, $this->user2])->filter();
    }

    /**
     * الحصول على المشارك الآخر في المحادثة
     */
    public function getOtherParticipant($userId)
    {
        if ($this->user1_id == $userId) {
            return $this->user2;
        } elseif ($this->user2_id == $userId) {
            return $this->user1;
        }
        return null;
    }

    /**
     * تحديد ما إذا كان المستخدم مشارك في المحادثة
     */
    public function hasParticipant($userId)
    {
        return $this->user1_id == $userId || $this->user2_id == $userId;
    }

    /**
     * الحصول على آخر رسالة في المحادثة
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * عدد الرسائل غير المقروءة للمستخدم
     */
    public function unreadMessagesCount($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * تحديد جميع الرسائل كمقروءة للمستخدم
     */
    public function markAllAsRead($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * إنشاء محادثة جديدة بين مستخدمين
     */
    public static function createBetweenUsers($user1Id, $user2Id, $type = 'private', $title = null)
    {
        // ترتيب المستخدمين لضمان عدم التكرار
        $sortedUsers = collect([$user1Id, $user2Id])->sort()->values();
        
        return static::firstOrCreate(
            [
                'user1_id' => $sortedUsers[0],
                'user2_id' => $sortedUsers[1]
            ],
            [
                'type' => $type,
                'title' => $title,
                'status' => 'open'
            ]
        );
    }

    /**
     * نطاق للمحادثات المفتوحة
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * نطاق للمحادثات المغلقة
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * نطاق لمحادثات المستخدم
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user1_id', $userId)
                    ->orWhere('user2_id', $userId);
    }

    /**
     * نطاق لنوع المحادثة
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * نطاق للمحادثات مع آخر رسالة
     */
    public function scopeWithLastMessage($query)
    {
        return $query->with(['lastMessage.sender']);
    }

    /**
     * تحديث وقت آخر رسالة
     */
    public function updateLastMessageTime()
    {
        $this->update(['last_message_at' => now()]);
    }

    /**
     * الحصول على عنوان المحادثة
     */
    public function getTitleForUser($userId)
    {
        if ($this->title) {
            return $this->title;
        }

        $otherUser = $this->getOtherParticipant($userId);
        return $otherUser ? $otherUser->name : 'محادثة';
    }
}