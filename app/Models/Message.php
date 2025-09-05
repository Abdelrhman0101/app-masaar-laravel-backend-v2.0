<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * الحقول المسموح بتعبئتها
     */
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'type',
        'is_read',
        'read_at',
        'metadata'
    ];

    /**
     * تحويل الحقول إلى أنواع البيانات المناسبة
     */
    protected $casts = [
        'read_at' => 'datetime',
        'is_read' => 'boolean',
        'metadata' => 'array'
    ];

    /**
     * القيم الافتراضية للحقول
     */
    protected $attributes = [
        'type' => 'text',
        'is_read' => false
    ];

    /**
     * أنواع الرسائل المسموحة
     */
    const MESSAGE_TYPES = [
        'text' => 'نص',
        'image' => 'صورة',
        'file' => 'ملف',
        'system' => 'رسالة نظام'
    ];

    /**
     * علاقة الرسالة بالمحادثة
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * علاقة الرسالة بالمرسل
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * تحديد ما إذا كانت الرسالة مقروءة
     */
    public function isRead()
    {
        return $this->is_read || !is_null($this->read_at);
    }

    /**
     * تحديد الرسالة كمقروءة
     */
    public function markAsRead()
    {
        if (!$this->isRead()) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
        return $this;
    }

    /**
     * تحديد ما إذا كانت الرسالة من النظام
     */
    public function isSystemMessage()
    {
        return $this->type === 'system';
    }

    /**
     * إنشاء رسالة نظام
     */
    public static function createSystemMessage($conversationId, $content, $metadata = [])
    {
        return static::create([
            'conversation_id' => $conversationId,
            'sender_id' => null, // رسائل النظام لا تحتاج مرسل
            'content' => $content,
            'type' => 'system',
            'is_read' => false,
            'metadata' => $metadata
        ]);
    }

    /**
     * نطاق للرسائل غير المقروءة
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * نطاق للرسائل المقروءة
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * نطاق لرسائل المستخدم
     */
    public function scopeFromUser($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }

    /**
     * نطاق لرسائل النظام
     */
    public function scopeSystemMessages($query)
    {
        return $query->where('type', 'system');
    }

    /**
     * نطاق للرسائل العادية (غير رسائل النظام)
     */
    public function scopeUserMessages($query)
    {
        return $query->where('type', '!=', 'system');
    }

    /**
     * ترتيب الرسائل حسب التاريخ (الأحدث أولاً)
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * ترتيب الرسائل حسب التاريخ (الأقدم أولاً)
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه حذف هذه الرسالة
     */
    public function canBeDeletedBy(User $user)
    {
        // يمكن للمرسل حذف رسالته أو للأدمن حذف أي رسالة
        return $this->sender_id === $user->id || $user->user_type === 'admin';
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه تعديل هذه الرسالة
     */
    public function canBeEditedBy(User $user)
    {
        // يمكن للمرسل فقط تعديل رسالته وليس رسائل النظام
        return $this->sender_id === $user->id && !$this->isSystemMessage();
    }

    /**
     * الحصول على محتوى الرسالة مع التنسيق المناسب
     */
    public function getFormattedContentAttribute()
    {
        if ($this->type === 'system') {
            return "🔔 {$this->content}";
        }
        
        return $this->content;
    }

    /**
     * الحصول على معلومات إضافية عن الرسالة
     */
    public function getMessageInfoAttribute()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'is_read' => $this->isRead(),
            'is_system' => $this->isSystemMessage(),
            'sent_at' => $this->created_at->format('Y-m-d H:i:s'),
            'read_at' => $this->read_at?->format('Y-m-d H:i:s'),
            'sender' => $this->sender ? [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'type' => $this->sender->user_type
            ] : null
        ];
    }
}