<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    // حذف المستخدم التجريبي إذا كان موجوداً
    User::where('email', 'test@example.com')->delete();
    
    // إنشاء مستخدم تجريبي غير معتمد
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
        'phone' => '1234567890',
        'user_type' => 'normal',
        'is_approved' => false,
        'is_email_verified' => true,
        'account_active' => true,
    ]);
    
    echo "تم إنشاء المستخدم التجريبي بنجاح:\n";
    echo "الاسم: {$user->name}\n";
    echo "البريد الإلكتروني: {$user->email}\n";
    echo "معرف المستخدم: {$user->id}\n";
    echo "حالة الموافقة: " . ($user->is_approved ? 'معتمد' : 'غير معتمد') . "\n";
    
} catch (Exception $e) {
    echo "خطأ في إنشاء المستخدم: " . $e->getMessage() . "\n";
}