<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Support\Notifier;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $isApiRequest = $request->expectsJson();

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::with([
            'carRental.officeDetail',
            'realEstate',
            'restaurantDetail',
        ])->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($isApiRequest) {
                return response()->json([
                    'status' => false,
                    'message' => 'بيانات الدخول غير صحيحة.',
                ], 401);
            }
            return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة.'])->withInput();
        }

        // Check if account is active (email verified)
        if (!$user->account_active || !$user->is_email_verified) {
            if ($isApiRequest) {
                return response()->json([
                    'status' => false,
                    'message' => 'الحساب غير مفعل. يرجى تأكيد بريدك الإلكتروني أولاً.',
                    'email_verification_required' => true,
                    'user_id' => $user->id
                ], 403);
            }
            return back()->withErrors(['email' => 'الحساب غير مفعل. يرجى تأكيد بريدك الإلكتروني أولاً.'])->withInput();
        }

        if (!$isApiRequest && $user->user_type !== 'admin') {
            return back()->withErrors(['email' => 'غير مسموح لك بالوصول إلى لوحة التحكم.'])->withInput();
        }

        if (!$isApiRequest) {
            Auth::login($user);
            return redirect()->route('dashboard');
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $extraData = [];
        switch ($user->user_type) {
            case 'real_estate_office':
            case 'real_estate_individual':
                $realEstate = $user->realEstate;
                if ($realEstate) {
                    $extraData['real_estate_id'] = $realEstate->id;
                    $extraData['real_estate'] = $realEstate;
                }
                break;
            case 'restaurant':
                $restaurant = $user->restaurantDetail;
                if ($restaurant) {
                    $extraData['restaurant_id'] = $restaurant->id;
                    $extraData['restaurant_detail'] = $restaurant;
                }
                break;
            case 'car_rental_office':
            case 'driver':
                $carRental = $user->carRental;
                if ($carRental) {
                    $extraData['car_rental_id'] = $carRental->id;
                    $extraData['car_rental'] = $carRental;
                }
                break;
        }

        // إرسال إشعار تلقائي للمستخدمين غير المعتمدين
        if (!$user->is_approved) {
            Notifier::send(
                $user,
                'account_pending_approval',
                'حسابك في انتظار الموافقة',
                'مرحباً بك! حسابك تم إنشاؤه بنجاح وهو الآن في انتظار موافقة الإدارة. سيتم إشعارك فور الموافقة على حسابك وتفعيل جميع الخدمات.'
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الدخول بنجاح.',
            'token' => $token,
            'user' => $user,
        ] + $extraData);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'تم تسجيل الخروج بنجاح.');
    }
}

?>