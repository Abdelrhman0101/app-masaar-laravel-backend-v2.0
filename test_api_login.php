<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Models\User;
use App\Models\Notification;

try {
    // البحث عن المستخدم التجريبي
    $user = User::where('email', 'test@example.com')->first();
    
    if (!$user) {
        echo "المستخدم التجريبي غير موجود!\n";
        exit(1);
    }
    
    echo "اختبار تسجيل الدخول عبر API للمستخدم: {$user->name}\n";
    echo "حالة الموافقة قبل تسجيل الدخول: " . ($user->is_approved ? 'معتمد' : 'غير معتمد') . "\n";
    
    // عدد الإشعارات قبل تسجيل الدخول
    $notificationsBefore = $user->notifications()->count();
    echo "عدد الإشعارات قبل تسجيل الدخول: {$notificationsBefore}\n";
    
    // إنشاء طلب تسجيل دخول مزيف
    $request = Request::create('/api/login', 'POST', [
        'email' => 'test@example.com',
        'password' => 'password123'
    ]);
    
    // إضافة header للإشارة إلى أنه طلب API
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('Content-Type', 'application/x-www-form-urlencoded');
    
    // تعيين البيانات في الطلب بشكل صحيح
    $request->merge([
        'email' => 'test@example.com',
        'password' => 'password123'
    ]);
    
    // تعيين session للطلب
    $request->setLaravelSession(app('session.store'));
    
    echo "بيانات الطلب: " . json_encode($request->all()) . "\n";
    
    // إنشاء controller وتنفيذ تسجيل الدخول
    $controller = new LoginController();
    $response = $controller->login($request);
    
    echo "\nنتيجة تسجيل الدخول:\n";
    echo "كود الاستجابة: " . $response->getStatusCode() . "\n";
    
    $responseData = json_decode($response->getContent(), true);
    if ($responseData) {
        echo "حالة النجاح: " . ($responseData['status'] ? 'نجح' : 'فشل') . "\n";
        echo "الرسالة: " . $responseData['message'] . "\n";
        
        if (isset($responseData['token'])) {
            echo "تم إنشاء التوكن بنجاح\n";
        }
    }
    
    // عدد الإشعارات بعد تسجيل الدخول
    $user->refresh(); // تحديث البيانات من قاعدة البيانات
    $notificationsAfter = $user->notifications()->count();
    echo "\nعدد الإشعارات بعد تسجيل الدخول: {$notificationsAfter}\n";
    
    // عرض آخر إشعار إذا تم إنشاء إشعار جديد
    if ($notificationsAfter > $notificationsBefore) {
        $lastNotification = $user->notifications()->latest()->first();
        echo "\nتم إنشاء إشعار جديد:\n";
        echo "العنوان: {$lastNotification->title}\n";
        echo "الرسالة: {$lastNotification->message}\n";
        echo "النوع: {$lastNotification->type}\n";
        echo "تاريخ الإنشاء: {$lastNotification->created_at}\n";
    } else {
        echo "\nلم يتم إنشاء إشعار جديد (ربما لأن المستخدم معتمد أو هناك خطأ)\n";
    }
    
} catch (Exception $e) {
    echo "خطأ في اختبار تسجيل الدخول: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}