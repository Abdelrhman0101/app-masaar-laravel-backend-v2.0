<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;

class SocialLoginController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // البحث عن المستخدم بالإيميل أو Google ID
            $user = User::where('email', $googleUser->getEmail())
                       ->orWhere('google_id', $googleUser->getId())
                       ->first();
            
            if ($user) {
                // تحديث Google ID إذا لم يكن موجود
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'login_type' => 'google',
                    ]);
                }
            } else {
                // إنشاء مستخدم جديد
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'login_type' => 'google',
                    'password' => bcrypt(Str::random(16)), // كلمة مرور عشوائية
                    'user_type' => 'normal', // نوع المستخدم الافتراضي
                    'is_email_verified' => true, // Google يؤكد الإيميل
                    'account_active' => true,
                    'is_approved' => true, // الموافقة التلقائية للمستخدمين العاديين
                ]);
            }
            
            // تسجيل دخول المستخدم
            Auth::login($user);
            
            return redirect()->intended('/dashboard');
            
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'حدث خطأ أثناء تسجيل الدخول بـ Google');
        }
    }
}