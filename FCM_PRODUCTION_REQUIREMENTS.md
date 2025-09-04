# متطلبات الخادم الإنتاجي لإرسال الإشعارات الخارجية عبر FCM

## 1. متطلبات Firebase Cloud Messaging

### أ) ملف بيانات الاعتماد (Service Account)
- **الملف المطلوب**: `msar-90137-bcc7d9a668bd.json`
- **المسار على الخادم**: `/home/msar/htdocs/msar.app/storage/app/msar-90137-bcc7d9a668bd.json`
- **الصلاحيات**: يجب أن يكون الملف قابل للقراءة من قبل خادم الويب
- **الأمان**: يجب حماية الملف من الوصول العام

### ب) متغيرات البيئة في `.env`
```env
# Firebase Cloud Messaging v1 Settings
FCM_PROJECT_ID=msar-90137
FCM_V1_CREDENTIALS=/home/msar/htdocs/msar.app/storage/app/msar-90137-bcc7d9a668bd.json
```

## 2. متطلبات الخادم

### أ) PHP Extensions المطلوبة
- **cURL**: لإرسال طلبات HTTP إلى Google FCM
- **OpenSSL**: للاتصال الآمن مع خوادم Google
- **JSON**: لمعالجة البيانات
- **mbstring**: لمعالجة النصوص العربية

### ب) Composer Dependencies
- **google/auth**: مكتبة Google للمصادقة
- **guzzlehttp/guzzle**: لإرسال طلبات HTTP

### ج) إعدادات الشبكة
- **الاتصال بالإنترنت**: يجب أن يكون الخادم قادر على الوصول إلى:
  - `https://fcm.googleapis.com`
  - `https://oauth2.googleapis.com`
  - `https://www.googleapis.com`
- **Firewall**: يجب السماح بالاتصالات الصادرة على المنافذ 80 و 443

## 3. إعدادات Laravel

### أ) تكوين الخدمات
التأكد من وجود إعدادات FCM في `config/services.php`:
```php
'fcm_v1' => [
    'project_id'  => env('FCM_PROJECT_ID'),
    'credentials' => env('FCM_V1_CREDENTIALS'),
],
```

### ب) تنظيف الكاش
بعد تحديث `.env`، يجب تشغيل:
```bash
php artisan config:clear
php artisan config:cache
```

## 4. اختبار الإعدادات

### أ) التحقق من وجود الملفات
```bash
# التحقق من وجود ملف بيانات الاعتماد
ls -la /home/msar/htdocs/msar.app/storage/app/msar-90137-bcc7d9a668bd.json

# التحقق من صلاحيات القراءة
cat /home/msar/htdocs/msar.app/storage/app/msar-90137-bcc7d9a668bd.json | head -5
```

### ب) اختبار الاتصال
```bash
# اختبار الوصول إلى خوادم Google
curl -I https://fcm.googleapis.com
curl -I https://oauth2.googleapis.com
```

### ج) اختبار PHP Extensions
```bash
php -m | grep -E "curl|openssl|json|mbstring"
```

## 5. مراقبة الأخطاء

### أ) سجلات Laravel
- **المسار**: `storage/logs/laravel.log`
- **البحث عن أخطاء FCM**:
```bash
tail -f storage/logs/laravel.log | grep FCM
```

### ب) سجلات الخادم
- **Apache/Nginx Error Logs**
- **PHP Error Logs**

## 6. الأمان والحماية

### أ) حماية ملف بيانات الاعتماد
```bash
# تعيين صلاحيات آمنة
chmod 600 /home/msar/htdocs/msar.app/storage/app/msar-90137-bcc7d9a668bd.json
chown www-data:www-data /home/msar/htdocs/msar.app/storage/app/msar-90137-bcc7d9a668bd.json
```

### ب) منع الوصول العام
إضافة `.htaccess` في مجلد `storage/app`:
```apache
Deny from all
```

## 7. استكشاف الأخطاء الشائعة

### أ) "Credentials file not found"
- التحقق من مسار الملف في `.env`
- التحقق من وجود الملف فعلياً
- التحقق من صلاحيات القراءة

### ب) "Invalid credentials"
- التحقق من صحة محتوى ملف JSON
- التحقق من صحة project_id
- التحقق من صلاحيات Service Account في Firebase Console

### ج) "Network connection failed"
- التحقق من اتصال الإنترنت
- التحقق من إعدادات Firewall
- التحقق من إعدادات Proxy إن وجدت

## 8. قائمة التحقق النهائية

- [ ] ملف بيانات الاعتماد موجود ومقروء
- [ ] متغيرات البيئة محدثة في `.env`
- [ ] تم تنظيف كاش Laravel
- [ ] PHP Extensions مثبتة
- [ ] الاتصال بخوادم Google يعمل
- [ ] صلاحيات الملفات صحيحة
- [ ] السجلات تظهر طلبات FCM
- [ ] لا توجد أخطاء في السجلات

## 9. أوامر الاختبار السريع

```bash
# اختبار شامل للإعدادات
php artisan tinker
>>> $service = new \App\Services\FcmHttpV1Service();
>>> $service->sendToToken('test_token', ['title' => 'Test', 'body' => 'Test message']);
```

إذا تم تنفيذ جميع هذه المتطلبات بشكل صحيح، فإن الخادم الإنتاجي سيكون مؤهلاً لإرسال الإشعارات الخارجية عبر FCM.