# دليل نشر Google OAuth على الخادم الإنتاجي

## نظرة عامة
هذا الدليل يوضح الخطوات المطلوبة لتفعيل Google OAuth على الخادم الإنتاجي. تم إضافة هذه الميزة حديثاً ولم يتم نشرها من قبل.

## الملفات الجديدة المضافة

### 1. ملفات التحكم (Controllers)
- `app/Http/Controllers/SocialLoginController.php` - تحكم في عملية تسجيل الدخول عبر Google

### 2. ملفات الأوامر (Artisan Commands)
- `app/Console/Commands/SimulateGoogleOAuth.php` - محاكاة عملية Google OAuth للاختبار
- `app/Console/Commands/CheckUser.php` - فحص بيانات المستخدمين

### 3. ملفات الاختبار (Tests)
- `tests/Feature/GoogleOAuthTest.php` - اختبارات Google OAuth

### 4. ملفات قاعدة البيانات (Migrations)
- `database/migrations/2025_01_25_000000_add_google_oauth_fields_to_users_table.php`

### 5. ملفات التوثيق والمحاكاة
- `simulate_google_oauth.php` - سكريبت محاكاة
- `GOOGLE_OAUTH_DEPLOYMENT_CHECKLIST.md` - قائمة مراجعة النشر

## خطوات النشر على الخادم الإنتاجي

### الخطوة 1: رفع الملفات الجديدة
```bash
# رفع جميع الملفات الجديدة إلى الخادم
git add .
git commit -m "Add Google OAuth integration"
git push origin main

# على الخادم الإنتاجي
git pull origin main
```

### الخطوة 2: تحديث Composer Dependencies
```bash
# على الخادم الإنتاجي
composer install --no-dev --optimize-autoloader
```

### الخطوة 3: تشغيل Migration قاعدة البيانات
```bash
# تشغيل migration الجديد
php artisan migrate

# التحقق من إضافة الحقول الجديدة
php artisan migrate:status
```

### الخطوة 4: إعداد متغيرات البيئة (.env)
أضف المتغيرات التالية إلى ملف `.env` على الخادم الإنتاجي:

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback

# تأكد من وجود هذه المتغيرات أيضاً
APP_URL=https://yourdomain.com
```

### الخطوة 5: إعداد Google Cloud Console

#### 5.1 إنشاء مشروع Google Cloud (إذا لم يكن موجوداً)
1. اذهب إلى [Google Cloud Console](https://console.cloud.google.com/)
2. أنشئ مشروع جديد أو اختر مشروع موجود
3. فعّل Google+ API أو Google Identity API

#### 5.2 إنشاء OAuth 2.0 Credentials
1. اذهب إلى "APIs & Services" > "Credentials"
2. انقر على "Create Credentials" > "OAuth 2.0 Client IDs"
3. اختر "Web application"
4. أضف Authorized redirect URIs:
   - `https://yourdomain.com/auth/google/callback`
   - `https://www.yourdomain.com/auth/google/callback` (إذا كنت تستخدم www)

#### 5.3 نسخ البيانات
- انسخ `Client ID` و `Client Secret`
- أضفهما إلى ملف `.env`

### الخطوة 6: تحديث التكوينات
```bash
# مسح cache التكوينات
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# إعادة تحميل التكوينات
php artisan config:cache
php artisan route:cache
```

### الخطوة 7: اختبار التكامل

#### 7.1 اختبار أساسي
```bash
# تشغيل اختبار Google OAuth
php artisan test tests/Feature/GoogleOAuthTest.php
```

#### 7.2 اختبار محاكاة
```bash
# محاكاة تسجيل دخول جديد
php artisan oauth:simulate-google

# فحص المستخدم المُنشأ
php artisan user:check test.user@gmail.com
```

### الخطوة 8: التحقق من الأمان

#### 8.1 التحقق من إعدادات قاعدة البيانات
- تأكد من إضافة الحقول الجديدة: `google_id`, `avatar`, `login_type`
- تحقق من أن `login_type` يقبل القيم: 'email', 'google'

#### 8.2 التحقق من الأمان
- المستخدمون عبر Google يتم إنشاؤهم بـ `user_type = 'normal'` فقط
- يتم تفعيل الحساب تلقائياً (`account_active = true`)
- يتم تأكيد البريد الإلكتروني تلقائياً (`is_email_verified = true`)
- يتم الموافقة على الحساب تلقائياً (`is_approved = true`)

## Routes الجديدة

تأكد من إضافة هذه Routes إلى `routes/web.php`:

```php
// Google OAuth Routes
Route::get('/auth/google', [SocialLoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback'])->name('auth.google.callback');
```

## اختبار الوظائف على الإنتاج

### 1. اختبار تسجيل الدخول
1. اذهب إلى `https://yourdomain.com/auth/google`
2. يجب أن يتم توجيهك إلى Google
3. بعد الموافقة، يجب أن يتم إنشاء حساب جديد أو تسجيل الدخول

### 2. اختبار قاعدة البيانات
```sql
-- التحقق من المستخدمين الجدد
SELECT id, name, email, google_id, login_type, user_type, is_approved, account_active 
FROM users 
WHERE login_type = 'google';
```

## استكشاف الأخطاء

### مشاكل شائعة وحلولها

#### 1. خطأ "Client ID not found"
- تحقق من `GOOGLE_CLIENT_ID` في `.env`
- تأكد من تشغيل `php artisan config:clear`

#### 2. خطأ "Redirect URI mismatch"
- تحقق من إعدادات Google Cloud Console
- تأكد من مطابقة `GOOGLE_REDIRECT_URI` مع المسجل في Google

#### 3. خطأ قاعدة البيانات
```bash
# تحقق من حالة migrations
php artisan migrate:status

# إعادة تشغيل migration إذا لزم الأمر
php artisan migrate:refresh --path=/database/migrations/2025_01_25_000000_add_google_oauth_fields_to_users_table.php
```

#### 4. مشاكل الصلاحيات
```bash
# تحقق من صلاحيات الملفات
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## ملاحظات مهمة

### الأمان
- لا تشارك `GOOGLE_CLIENT_SECRET` أبداً
- استخدم HTTPS على الإنتاج دائماً
- تحقق من إعدادات CORS إذا لزم الأمر

### الأداء
- استخدم `config:cache` على الإنتاج
- تأكد من تفعيل OPcache

### المراقبة
- راقب logs Laravel في `storage/logs/`
- راقب أخطاء قاعدة البيانات
- تتبع معدلات تسجيل الدخول الجديدة

## الدعم والصيانة

### أوامر مفيدة للصيانة
```bash
# فحص مستخدم معين
php artisan user:check email@example.com

# محاكاة تسجيل دخول للاختبار
php artisan oauth:simulate-google

# تشغيل جميع الاختبارات
php artisan test
```

### مراجعة دورية
- تحقق من صحة Google OAuth tokens شهرياً
- راجع إعدادات Google Cloud Console
- تحديث dependencies بانتظام

---

**تاريخ الإنشاء:** $(date)
**الإصدار:** 1.0
**المطور:** تم التطوير بواسطة AI Assistant

**ملاحظة:** هذا التكامل جديد تماماً ولم يتم نشره من قبل. تأكد من اتباع جميع الخطوات بعناية.