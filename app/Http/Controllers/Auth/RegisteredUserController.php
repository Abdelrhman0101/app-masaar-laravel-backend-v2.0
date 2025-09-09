<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Notifications\EmailVerificationOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Support\Notifier;

class RegisteredUserController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function store(Request $request)
    {
        // 1. التحقق الأساسي من بيانات المستخدم العامة
       $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users',
            'password'    => 'required|string|min:6',
            'phone'       => 'required|string|max:20|unique:users',
            'governorate' => 'nullable|string|max:255',
            'user_type'   => 'required|in:normal,real_estate_office,real_estate_individual,restaurant,car_rental_office,driver,admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // 2. إنشاء المستخدم في جدول users (غير مفعل)
        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'phone'       => $request->phone,
            'governorate' => $request->governorate,
            'user_type'   => $request->user_type,
            // تفعيل حسابات الـ admin تلقائياً
            'is_email_verified' => $request->user_type === 'admin' ? true : false,
            'account_active' => $request->user_type === 'admin' ? true : false,
            'email_verified_at' => $request->user_type === 'admin' ? now() : null,
        ]);

        // 3. تفريع التسجيل حسب نوع المستخدم
        switch ($user->user_type) {
            case 'normal':
                $user->normalUser()->create([]);
                break;

            case 'real_estate_office':
                $realEstate = $user->realEstate()->create(['type' => 'office']);
                $realEstate->officeDetail()->create([
                    'office_name' => $request->office_name,
                    'office_address' => $request->office_address,
                    'office_phone' => $request->office_phone,
                    'logo_image' => $request->logo_image,
                    'owner_id_front_image' => $request->owner_id_front_image,
                    'owner_id_back_image' => $request->owner_id_back_image,
                    'office_image' => $request->office_image,
                    'commercial_register_front_image' => $request->commercial_register_front_image,
                    'commercial_register_back_image' => $request->commercial_register_back_image,
                    'tax_enabled' => $request->tax_enabled ?? false,
                ]);
                break;

            case 'real_estate_individual':
                $realEstate = $user->realEstate()->create(['type' => 'individual']);
                $realEstate->individualDetail()->create([
                    'profile_image' => $request->profile_image,
                    'agent_name' => $request->agent_name,
                    'agent_id_front_image' => $request->agent_id_front_image,
                    'agent_id_back_image' => $request->agent_id_back_image,
                    'tax_card_front_image' => $request->tax_card_front_image,
                    'tax_card_back_image' => $request->tax_card_back_image,
                ]);
                break;

            case 'restaurant':
                $user->restaurantDetail()->create([
                    'profile_image' => $request->profile_image,
                    'restaurant_name' => $request->restaurant_name,
                    'logo_image' => $request->logo_image,
                    'owner_id_front_image' => $request->owner_id_front_image,
                    'owner_id_back_image' => $request->owner_id_back_image,
                    'license_front_image' => $request->license_front_image,
                    'license_back_image' => $request->license_back_image,
                    'commercial_register_front_image' => $request->commercial_register_front_image,
                    'commercial_register_back_image' => $request->commercial_register_back_image,
                    'vat_included' => $request->vat_included ?? false,
                    'vat_image_front' => $request->vat_image_front,
                    'vat_image_back' => $request->vat_image_back,
                    'cuisine_types' => $request->cuisine_types,
                    'branches' => $request->branches,
                    'delivery_available' => $request->delivery_available ?? false,
                    'delivery_cost_per_km' => $request->delivery_cost_per_km,
                    'table_reservation_available' => $request->table_reservation_available ?? false,
                    'max_people_per_reservation' => $request->max_people_per_reservation,
                    'reservation_notes' => $request->reservation_notes,
                    'deposit_required' => $request->deposit_required ?? false,
                    'deposit_amount' => $request->deposit_amount,
                    'working_hours' => $request->working_hours,
                ]);
                break;

            case 'car_rental_office':
                $carRental = $user->carRental()->create(['rental_type' => 'office']);
                $carRental->officeDetail()->create([
                    'office_name' => $request->office_name,
                    'logo_image' => $request->logo_image,
                    'commercial_register_front_image' => $request->commercial_register_front_image,
                    'commercial_register_back_image' => $request->commercial_register_back_image,
                    'payment_methods' => $request->payment_methods,
                    'rental_options' => $request->rental_options,
                    'cost_per_km' => $request->cost_per_km,
                    'daily_driver_cost' => $request->daily_driver_cost,
                    'max_km_per_day' => $request->max_km_per_day,
                ]);
                break;

            case 'driver':
                $carRental = $user->carRental()->create(['rental_type' => 'driver']);
                $carRental->driverDetail()->create([
                    'profile_image' => $request->profile_image,
                    'payment_methods' => $request->payment_methods,
                    'rental_options' => $request->rental_options,
                    'cost_per_km' => $request->cost_per_km,
                    'daily_driver_cost' => $request->daily_driver_cost,
                    'max_km_per_day' => $request->max_km_per_day,
                    
                ]);
                break;
        }

        // 3.5 إرسال إشعار إلى جميع المدراء بوجود تسجيل مستخدم جديد (ما عدا حسابات الـ admin)
        try {
            if ($user->user_type !== 'admin') {
                $admins = User::where('user_type', 'admin')->get();
                foreach ($admins as $admin) {
                    Notifier::send(
                        $admin,
                        'user_registered',
                        'تسجيل مستخدم جديد',
                        'قام المستخدم ' . $user->name . ' بالتسجيل.',
                        ['user_id' => (string)$user->id, 'user_type' => $user->user_type],
                        '/accounts?user_id=' . $user->id
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send admin registration notification', ['error' => $e->getMessage(), 'new_user_id' => $user->id]);
        }

        // 4. إرسال رمز التحقق عبر البريد الإلكتروني (فقط للمستخدمين غير الـ admin)
        if ($user->user_type === 'admin') {
            // إرجاع استجابة مباشرة للـ admin بدون OTP
            return response()->json([
                'status' => true,
                'message' => 'تم إنشاء حساب الإدارة بنجاح وتم تفعيله تلقائياً.',
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'email_verification_required' => false,
                'account_active' => true
            ], 201);
        }

        // للمستخدمين العاديين - إرسال OTP
        try {
            $otpResult = $this->otpService->generateEmailVerificationOtp($user);
            
            if ($otpResult['success']) {
                $user->notify(new EmailVerificationOtp(
                    $otpResult['otp'],
                    $otpResult['expires_at'],
                    $user->name
                ));
                
                return response()->json([
                    'status' => true,
                    'message' => 'تم التسجيل بنجاح. يرجى تأكيد بريدك الإلكتروني لتفعيل الحساب.',
                    'user_id' => $user->id,
                    'user_type' => $user->user_type,
                    'email_verification_required' => true,
                    'otp_expires_at' => $otpResult['expires_at']->toISOString()
                ], 201);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'تم التسجيل ولكن فشل في إرسال رمز التحقق. يرجى طلب رمز جديد.',
                    'user_id' => $user->id,
                    'user_type' => $user->user_type,
                    'email_verification_required' => true
                ], 201);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send email verification OTP during registration', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'تم التسجيل ولكن فشل في إرسال رمز التحقق. يرجى طلب رمز جديد.',
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'email_verification_required' => true
            ], 201);
        }

    }

}
