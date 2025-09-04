<?php

require_once __DIR__ . '/vendor/autoload.php';

// تحميل Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

echo "=== فحص إعدادات FCM ===\n\n";

// 1. فحص متغيرات البيئة
echo "1. فحص متغيرات البيئة FCM:\n";
echo "FCM_PROJECT_ID: " . (env('FCM_PROJECT_ID') ?: 'غير موجود') . "\n";
echo "FCM_V1_CREDENTIALS: " . (env('FCM_V1_CREDENTIALS') ?: 'غير موجود') . "\n";
echo "FCM_SERVER_KEY: " . (env('FCM_SERVER_KEY') ?: 'غير موجود') . "\n\n";

// 2. فحص إعدادات config/services.php
echo "2. فحص إعدادات config/services.php:\n";
echo "FCM V1 Project ID: " . (config('services.fcm_v1.project_id') ?: 'غير موجود') . "\n";
echo "FCM V1 Credentials: " . (config('services.fcm_v1.credentials') ?: 'غير موجود') . "\n";
echo "FCM Server Key: " . (config('services.fcm.server_key') ?: 'غير موجود') . "\n\n";

// 3. فحص وجود ملف الاعتمادات
$credentialsPath = config('services.fcm_v1.credentials');
if ($credentialsPath) {
    echo "3. فحص ملف الاعتمادات:\n";
    echo "مسار الملف: $credentialsPath\n";
    echo "الملف موجود: " . (file_exists($credentialsPath) ? 'نعم' : 'لا') . "\n";
    if (file_exists($credentialsPath)) {
        echo "حجم الملف: " . filesize($credentialsPath) . " بايت\n";
        echo "قابل للقراءة: " . (is_readable($credentialsPath) ? 'نعم' : 'لا') . "\n";
    }
    echo "\n";
} else {
    echo "3. ملف الاعتمادات غير محدد في الإعدادات\n\n";
}

// 4. فحص جدول device_tokens
echo "4. فحص جدول device_tokens:\n";
try {
    $totalTokens = DB::table('device_tokens')->count();
    echo "إجمالي رموز الأجهزة: $totalTokens\n";
    
    $enabledTokens = DB::table('device_tokens')->where('is_enabled', 1)->count();
    echo "الرموز المفعلة: $enabledTokens\n";
    
    $disabledTokens = DB::table('device_tokens')->where('is_enabled', 0)->count();
    echo "الرموز المعطلة: $disabledTokens\n";
    
    // عرض آخر 5 رموز مسجلة
    $recentTokens = DB::table('device_tokens')
        ->join('users', 'device_tokens.user_id', '=', 'users.id')
        ->select('device_tokens.*', 'users.email')
        ->orderBy('device_tokens.created_at', 'desc')
        ->limit(5)
        ->get();
    
    echo "\nآخر 5 رموز مسجلة:\n";
    foreach ($recentTokens as $token) {
        echo "- المستخدم: {$token->email}\n";
        echo "  المنصة: {$token->platform}\n";
        echo "  مفعل: " . ($token->is_enabled ? 'نعم' : 'لا') . "\n";
        echo "  تاريخ التسجيل: {$token->created_at}\n";
        echo "  الرمز: " . substr($token->token, 0, 50) . "...\n\n";
    }
    
} catch (Exception $e) {
    echo "خطأ في الوصول لجدول device_tokens: " . $e->getMessage() . "\n\n";
}

// 5. فحص المستخدم التجريبي
echo "5. فحص المستخدم التجريبي:\n";
try {
    $testUser = User::where('email', 'test@example.com')->first();
    if ($testUser) {
        echo "المستخدم التجريبي موجود\n";
        echo "معرف المستخدم: {$testUser->id}\n";
        echo "الحالة المعتمدة: " . ($testUser->is_approved ? 'معتمد' : 'غير معتمد') . "\n";
        echo "الإشعارات مفعلة: " . ($testUser->push_notifications_enabled ? 'نعم' : 'لا') . "\n";
        
        // فحص رموز الأجهزة للمستخدم التجريبي
        $userTokens = DB::table('device_tokens')
            ->where('user_id', $testUser->id)
            ->get();
        
        echo "عدد رموز الأجهزة: " . count($userTokens) . "\n";
        foreach ($userTokens as $token) {
            echo "  - المنصة: {$token->platform}, مفعل: " . ($token->is_enabled ? 'نعم' : 'لا') . "\n";
        }
    } else {
        echo "المستخدم التجريبي غير موجود\n";
    }
} catch (Exception $e) {
    echo "خطأ في فحص المستخدم التجريبي: " . $e->getMessage() . "\n";
}

echo "\n=== انتهى الفحص ===\n";