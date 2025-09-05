# نظام المحادثات المتقدم - Masaar Backend

## نظرة عامة

تم تطوير نظام محادثات شامل ومرن يدعم جميع أنواع التواصل في تطبيق Masaar، بما في ذلك:
- محادثات المستخدمين مع الدعم الفني (Admin-User)
- محادثات المستخدمين مع مقدمي الخدمات (User-Provider)
- محادثات المستخدمين مع بعضهم البعض (User-User)

## الميزات الرئيسية

### 🔥 الميزات الأساسية
- ✅ إرسال واستقبال الرسائل النصية
- ✅ دعم الصور والملفات
- ✅ رسائل النظام التلقائية
- ✅ حالات المحادثة (مفتوحة، مغلقة، مؤرشفة)
- ✅ تتبع حالة قراءة الرسائل
- ✅ البث المباشر للرسائل (Real-time)

### 🚀 الميزات المتقدمة
- ✅ مؤشرات الكتابة (Typing Indicators)
- ✅ حالة المستخدم (Online/Offline/Away)
- ✅ إشعارات فورية (Push Notifications)
- ✅ إشعارات البريد الإلكتروني
- ✅ تنظيف البيانات التلقائي
- ✅ إحصائيات شاملة للمدراء

## هيكل قاعدة البيانات

### جدول المحادثات (conversations)
```sql
- id: معرف المحادثة
- user1_id: المستخدم الأول
- user2_id: المستخدم الثاني (اختياري)
- admin_id: معرف المدير (للمحادثات الإدارية)
- type: نوع المحادثة (admin_user, user_provider, user_user)
- status: حالة المحادثة (open, closed, archived)
- title: عنوان المحادثة
- last_message_at: وقت آخر رسالة
- metadata: بيانات إضافية (JSON)
- created_at, updated_at, deleted_at
```

### جدول الرسائل (messages)
```sql
- id: معرف الرسالة
- conversation_id: معرف المحادثة
- sender_id: معرف المرسل
- content: محتوى الرسالة
- type: نوع الرسالة (text, image, file, system)
- is_read: هل تم قراءة الرسالة
- read_at: وقت القراءة
- metadata: بيانات إضافية (JSON)
- created_at, updated_at, deleted_at
```

## API Endpoints

### المحادثات
```
GET    /api/conversations              # قائمة المحادثات
POST   /api/conversations              # إنشاء محادثة جديدة
GET    /api/conversations/{id}         # تفاصيل محادثة
PUT    /api/conversations/{id}/status  # تحديث حالة المحادثة
DELETE /api/conversations/{id}         # أرشفة المحادثة
POST   /api/conversations/{id}/mark-all-read # تعليم جميع الرسائل كمقروءة
```

### الرسائل
```
GET    /api/conversations/{id}/messages # رسائل المحادثة
POST   /api/conversations/{id}/messages # إرسال رسالة جديدة
PUT    /api/messages/{id}              # تعديل رسالة
DELETE /api/messages/{id}              # حذف رسالة
POST   /api/messages/{id}/mark-read    # تعليم رسالة كمقروءة
```

### الميزات المتقدمة
```
POST   /api/conversations/{id}/typing           # إشعار الكتابة
GET    /api/conversations/{id}/typing-users     # المستخدمون الذين يكتبون
GET    /api/conversations/{id}/participants-status # حالة المشاركين
POST   /api/user/status                         # تحديث حالة المستخدم
GET    /api/user/{id}/status                    # حالة مستخدم محدد
POST   /api/user/heartbeat                      # نبضة النشاط
```

### المدراء
```
GET    /api/admin/conversations/statistics     # إحصائيات المحادثات
POST   /api/admin/conversations/{id}/system-message # إرسال رسالة نظام
```

## الأحداث والبث المباشر

### الأحداث المتاحة
- `NewMessage`: رسالة جديدة
- `UserTyping`: مؤشر الكتابة
- `UserStatusChanged`: تغيير حالة المستخدم

### قنوات البث
- `chat.{conversationId}`: قناة المحادثة
- `user.{userId}`: قناة المستخدم الشخصية
- `admin.notifications`: قناة إشعارات المدراء
- `system.announcements`: قناة الإعلانات العامة

## التكوين

### ملف التكوين: `config/conversation.php`
```php
// أنواع المحادثات
'types' => [
    'admin_user' => ['features' => ['system_messages', 'email_notifications']],
    'user_provider' => ['features' => ['file_sharing', 'rating']],
    'user_user' => ['features' => ['basic_chat']]
],

// الميزات المتقدمة
'features' => [
    'typing_indicators' => true,
    'user_status' => true,
    'message_reactions' => false,
    'auto_translation' => false
],

// التنظيف التلقائي
'cleanup' => [
    'enabled' => true,
    'soft_deleted_messages_days' => 30,
    'archived_conversations_days' => 90
]
```

## الإشعارات

### أنواع الإشعارات
1. **إشعارات قاعدة البيانات**: تُحفظ في جدول `notifications`
2. **إشعارات FCM**: للهواتف المحمولة
3. **إشعارات البريد الإلكتروني**: للمحادثات المهمة

### تخصيص الإشعارات
```php
// في NewMessageNotification
public function shouldSend(object $notifiable, string $channel): bool
{
    // منطق تحديد متى يتم إرسال الإشعار
    return $this->customLogic($notifiable, $channel);
}
```

## الأمان والصلاحيات

### Middleware المستخدمة
- `auth:sanctum`: التحقق من الهوية
- `conversation.participant`: التحقق من المشاركة في المحادثة
- `is_admin`: التحقق من صلاحيات المدير

### قواعد الوصول
1. المستخدمون يمكنهم الوصول فقط للمحادثات التي يشاركون فيها
2. المدراء يمكنهم الوصول لجميع المحادثات
3. مقدمو الخدمات يمكنهم التواصل مع المستخدمين فقط

## التشغيل والصيانة

### تشغيل المهام المجدولة
```bash
# تنظيف البيانات القديمة
php artisan conversation:cleanup

# تنظيف تجريبي (بدون حذف فعلي)
php artisan conversation:cleanup --dry-run

# تنظيف إجباري (بدون تأكيد)
php artisan conversation:cleanup --force
```

### مراقبة الأداء
```bash
# مراقبة الطوابير
php artisan queue:work

# مراقبة البث المباشر
php artisan websockets:serve
```

## استكشاف الأخطاء

### مشاكل شائعة وحلولها

1. **الرسائل لا تصل في الوقت الفعلي**
   - تأكد من تشغيل WebSocket server
   - تحقق من إعدادات Broadcasting في `.env`

2. **الإشعارات لا تُرسل**
   - تأكد من تشغيل Queue worker
   - تحقق من إعدادات FCM

3. **مشاكل الصلاحيات**
   - تأكد من تسجيل Middleware بشكل صحيح
   - تحقق من العلاقات في Models

### سجلات النظام
```bash
# عرض سجلات Laravel
tail -f storage/logs/laravel.log

# عرض سجلات المحادثات
grep "conversation" storage/logs/laravel.log
```

## التطوير المستقبلي

### ميزات مقترحة
- [ ] ردود الأفعال على الرسائل (Reactions)
- [ ] إعادة توجيه الرسائل
- [ ] البحث في المحادثات
- [ ] الترجمة التلقائية
- [ ] المكالمات الصوتية/المرئية
- [ ] مشاركة الموقع
- [ ] الرسائل المجدولة

### تحسينات الأداء
- [ ] تخزين مؤقت للمحادثات النشطة
- [ ] ضغط الرسائل القديمة
- [ ] فهرسة محسنة لقاعدة البيانات
- [ ] تحسين استعلامات البحث

## المساهمة

عند إضافة ميزات جديدة:
1. اتبع معايير الكود الموجودة
2. أضف اختبارات للميزات الجديدة
3. حدث التوثيق
4. تأكد من التوافق مع الإصدارات السابقة

## الدعم

للحصول على المساعدة:
- راجع سجلات النظام أولاً
- تحقق من إعدادات التكوين
- استخدم أدوات التشخيص المتاحة

---

**تم تطوير هذا النظام بواسطة فريق Masaar Development Team**

*آخر تحديث: يناير 2024*