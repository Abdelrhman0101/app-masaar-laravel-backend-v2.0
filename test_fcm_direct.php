<?php

require_once __DIR__ . '/vendor/autoload.php';

// تحميل Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FcmHttpV1Service;
use Illuminate\Support\Facades\Log;

echo "=== اختبار FCM مباشرة ===\n\n";

// 1. فحص إعدادات FCM
echo "1. فحص إعدادات FCM:\n";
$projectId = config('services.fcm_v1.project_id');
$credentialsPath = config('services.fcm_v1.credentials');

echo "   Project ID: " . ($projectId ?: 'غير موجود') . "\n";
echo "   Credentials Path: " . ($credentialsPath ?: 'غير موجود') . "\n";

if ($credentialsPath) {
    echo "   ملف الاعتمادات موجود: " . (file_exists($credentialsPath) ? 'نعم' : 'لا') . "\n";
}
echo "\n";

// 2. محاولة إنشاء خدمة FCM
echo "2. محاولة إنشاء خدمة FCM:\n";
try {
    $fcmService = new FcmHttpV1Service();
    echo "   تم إنشاء خدمة FCM بنجاح\n";
} catch (Exception $e) {
    echo "   خطأ في إنشاء خدمة FCM: " . $e->getMessage() . "\n";
    echo "   انتهى الاختبار بسبب الخطأ\n";
    exit(1);
}
echo "\n";

// 3. اختبار الحصول على Access Token
echo "3. اختبار الحصول على Access Token:\n";
try {
    // استخدام reflection للوصول للدالة المحمية
    $reflection = new ReflectionClass($fcmService);
    $method = $reflection->getMethod('accessToken');
    $method->setAccessible(true);
    
    $token = $method->invoke($fcmService);
    echo "   تم الحصول على Access Token بنجاح\n";
    echo "   طول الرمز: " . strlen($token) . " حرف\n";
    echo "   بداية الرمز: " . substr($token, 0, 20) . "...\n";
} catch (Exception $e) {
    echo "   خطأ في الحصول على Access Token: " . $e->getMessage() . "\n";
    echo "   هذا يعني أن ملف الاعتمادات غير صحيح أو غير موجود\n";
    echo "   انتهى الاختبار بسبب الخطأ\n";
    exit(1);
}
echo "\n";

// 4. اختبار إرسال إشعار لرمز وهمي
echo "4. اختبار إرسال إشعار لرمز وهمي:\n";
$fakeToken = 'fake_test_token_for_debugging';

try {
    $result = $fcmService->sendToToken(
        $fakeToken,
        [
            'title' => 'اختبار الإشعار',
            'body' => 'هذا اختبار للتأكد من عمل FCM'
        ],
        [
            'type' => 'test',
            'timestamp' => (string) time()
        ]
    );
    
    echo "   تم إرسال الطلب إلى FCM\n";
    echo "   النتيجة: " . json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "   خطأ في إرسال الإشعار: " . $e->getMessage() . "\n";
}
echo "\n";

// 5. فحص السجلات الأخيرة
echo "5. فحص السجلات الأخيرة:\n";
echo "   تحقق من ملف السجلات للحصول على تفاصيل أكثر\n";
echo "   ابحث عن: [FCMv1] Sending Push Notification Request\n";
echo "   ابحث عن: [FCMv1] Received Response from Google\n";

echo "\n=== انتهى الاختبار ===\n";

echo "\nتوصيات لحل المشكلة:\n";
echo "1. تأكد من وجود ملف اعتمادات FCM الصحيح\n";
echo "2. تأكد من صحة مسار الملف في متغير FCM_V1_CREDENTIALS\n";
echo "3. تأكد من صحة Project ID في متغير FCM_PROJECT_ID\n";
echo "4. تأكد من أن الملف قابل للقراءة من قبل خادم الويب\n";
echo "5. في البيئة المحلية، قم بنسخ ملف الاعتمادات من الخادم\n";