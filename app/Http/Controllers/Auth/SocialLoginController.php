<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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

    /**
     * Handle Google Sign In from Mobile App
     */
    public function handleGoogleMobileLogin(Request $request)
    {
        try {
            // التحقق من صحة البيانات المرسلة
            $request->validate([
                'google_id' => 'required|string',
                'email' => 'required|email',
                'name' => 'required|string',
                'avatar' => 'nullable|string',
            ]);

            // البحث عن المستخدم بالإيميل أو Google ID
            $user = User::where('email', $request->email)
                       ->orWhere('google_id', $request->google_id)
                       ->first();

            if ($user) {
                // التحقق من أن المستخدم من النوع العادي فقط
                if ($user->user_type !== 'normal') {
                    return response()->json([
                        'status' => false,
                        'message' => 'تسجيل الدخول عبر Google متاح للمستخدمين العاديين فقط'
                    ], 403);
                }

                // تحديث Google ID والصورة إذا لم تكن موجودة
                if (!$user->google_id || $user->avatar !== $request->avatar) {
                    $user->update([
                        'google_id' => $request->google_id,
                        'avatar' => $request->avatar,
                        'login_type' => 'google',
                    ]);
                }
            } else {
                // إنشاء مستخدم جديد (نوع عادي فقط)
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'google_id' => $request->google_id,
                    'avatar' => $request->avatar,
                    'login_type' => 'google',
                    'password' => bcrypt(Str::random(16)), // كلمة مرور عشوائية
                    'user_type' => 'normal', // نوع المستخدم الافتراضي
                    'is_email_verified' => true, // Google يؤكد الإيميل
                    'account_active' => true,
                    'is_approved' => true, // الموافقة التلقائية للمستخدمين العاديين
                ]);
            }

            // إنشاء توكن API للمستخدم
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => $user->wasRecentlyCreated ? 'تم إنشاء الحساب بنجاح' : 'تم تسجيل الدخول بنجاح',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'avatar' => $user->avatar,
                    'login_type' => $user->login_type,
                    'is_email_verified' => $user->is_email_verified,
                    'account_active' => $user->account_active,
                    'is_approved' => $user->is_approved,
                ],
                'token' => $token
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء تسجيل الدخول عبر Google: ' . $e->getMessage()
            ], 500);
        }
    }
}