<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Notification;
use App\Support\Notifier;
use Illuminate\Support\Facades\Hash;

try {
    // البحث عن المستخدم التجريبي
    $user = User::where('email', 'test@example.com')->first();
    
    if (!$user) {
        echo "المستخدم التجريبي غير موجود!\n";
        exit(1);
    }
    
    echo "تم العثور على المستخدم: {$user->name}\n";
    echo "حالة الموافقة: " . ($user->is_approved ? 'معتمد' : 'غير معتمد') . "\n";
    
    // عدد الإشعارات قبل الاختبار
    $notificationsBefore = $user->notifications()->count();
    echo "عدد الإشعارات قبل الاختبار: {$notificationsBefore}\n";
    
    // محاكاة الكود الذي أضفناه في LoginController
    if (!$user->is_approved) {
        echo "\nإرسال إشعار للمستخدم غير المعتمد...\n";
        
        Notifier::send(
            $user,
            'account_pending_approval',
            'حسابك في انتظار الموافقة',
            'مرحباً بك! حسابك تم إنشاؤه بنجاح وهو الآن في انتظار موافقة الإدارة. سيتم إشعارك فور الموافقة على حسابك وتفعيل جميع الخدمات.'
        );
        
        echo "تم إرسال الإشعار بنجاح!\n";
    }
    
    // عدد الإشعارات بعد الاختبار
    $notificationsAfter = $user->notifications()->count();
    echo "عدد الإشعارات بعد الاختبار: {$notificationsAfter}\n";
    
    // عرض آخر إشعار
    $lastNotification = $user->notifications()->latest()->first();
    if ($lastNotification) {
        echo "\nآخر إشعار:\n";
        echo "العنوان: {$lastNotification->title}\n";
        echo "الرسالة: {$lastNotification->message}\n";
        echo "النوع: {$lastNotification->type}\n";
        echo "تاريخ الإنشاء: {$lastNotification->created_at}\n";
    }
    
} catch (Exception $e) {
    echo "خطأ في الاختبار: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}