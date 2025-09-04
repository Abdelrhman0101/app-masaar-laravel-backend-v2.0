<?php

require_once __DIR__ . '/vendor/autoload.php';

// تحميل Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Support\Notifier;

echo "=== اختبار الإشعارات الخارجية ===\n\n";

// 1. البحث عن المستخدم التجريبي
$testUser = User::where('email', 'test@example.com')->first();
if (!$testUser) {
    echo "خطأ: المستخدم التجريبي غير موجود\n";
    exit(1);
}

echo "1. المستخدم التجريبي:\n";
echo "   المعرف: {$testUser->id}\n";
echo "   البريد الإلكتروني: {$testUser->email}\n";
echo "   الحالة: " . ($testUser->is_approved ? 'معتمد' : 'غير معتمد') . "\n";
echo "   الإشعارات مفعلة: " . ($testUser->push_notifications_enabled ? 'نعم' : 'لا') . "\n\n";

// 2. إضافة رمز جهاز وهمي للاختبار
echo "2. إضافة رمز جهاز وهمي للاختبار:\n";
$fakeToken = 'fake_fcm_token_' . time() . '_android';

// حذف أي رموز سابقة للمستخدم التجريبي
DB::table('device_tokens')->where('user_id', $testUser->id)->delete();

// إضافة رمز جديد
DB::table('device_tokens')->insert([
    'user_id' => $testUser->id,
    'token' => $fakeToken,
    'platform' => 'android',
    'is_enabled' => 1,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "   تم إضافة رمز الجهاز: " . substr($fakeToken, 0, 30) . "...\n\n";

// 3. فحص عدد الإشعارات قبل الإرسال
echo "3. فحص الإشعارات قبل الإرسال:\n";
$notificationsBefore = DB::table('notifications')
    ->where('user_id', $testUser->id)
    ->count();
echo "   عدد الإشعارات الحالية: $notificationsBefore\n\n";

// 4. إرسال إشعار للحساب غير المعتمد
echo "4. إرسال إشعار للحساب غير المعتمد:\n";
try {
    $result = Notifier::send(
        $testUser,
        'account_pending_approval',
        'حسابك في انتظار الموافقة',
        'حسابك قيد المراجعة من قبل الإدارة. سيتم إشعارك عند الموافقة عليه.'
    );
    
    echo "   تم إرسال الإشعار بنجاح\n";
    echo "   النتيجة: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n\n";
    
} catch (Exception $e) {
    echo "   خطأ في إرسال الإشعار: " . $e->getMessage() . "\n\n";
}

// 5. فحص عدد الإشعارات بعد الإرسال
echo "5. فحص الإشعارات بعد الإرسال:\n";
$notificationsAfter = DB::table('notifications')
    ->where('user_id', $testUser->id)
    ->count();
echo "   عدد الإشعارات الجديدة: $notificationsAfter\n";
echo "   الفرق: " . ($notificationsAfter - $notificationsBefore) . "\n\n";

// 6. عرض آخر إشعار تم إرساله
echo "6. آخر إشعار تم إرساله:\n";
$lastNotification = DB::table('notifications')
    ->where('user_id', $testUser->id)
    ->orderBy('created_at', 'desc')
    ->first();

if ($lastNotification) {
    echo "   العنوان: {$lastNotification->title}\n";
    echo "   الرسالة: {$lastNotification->message}\n";
    echo "   النوع: {$lastNotification->type}\n";
    echo "   تاريخ الإرسال: {$lastNotification->created_at}\n\n";
} else {
    echo "   لا توجد إشعارات\n\n";
}

// 7. فحص سجلات FCM
echo "7. فحص سجلات FCM:\n";
echo "   تحقق من ملف السجلات: storage/logs/laravel.log\n";
echo "   ابحث عن: [FCMv1] Sending Push Notification Request\n\n";

// 8. تنظيف - حذف رمز الجهاز الوهمي
echo "8. تنظيف البيانات:\n";
DB::table('device_tokens')->where('token', $fakeToken)->delete();
echo "   تم حذف رمز الجهاز الوهمي\n\n";

echo "=== انتهى الاختبار ===\n";
echo "\nملاحظات مهمة:\n";
echo "- إذا لم تظهر الإشعارات الخارجية، تأكد من:\n";
echo "  1. وجود ملف اعتمادات FCM الصحيح\n";
echo "  2. صحة إعدادات FCM في ملف البيئة\n";
echo "  3. تسجيل رمز جهاز حقيقي من التطبيق\n";
echo "  4. تفعيل الإشعارات في إعدادات الجهاز\n";