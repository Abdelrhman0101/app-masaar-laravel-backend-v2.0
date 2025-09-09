<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Support\Notifier;

class UserController extends Controller
{
    // استعراض كل المستخدمين مع العلاقات
    public function index()
    {
        $users = User::with([
            'normalUser',
            'realEstate.officeDetail',
            'realEstate.individualDetail',
            'restaurantDetail',
            'carRental.officeDetail',
            'carRental.driverDetail'
        ])->get();

        return response()->json([
            'status' => true,
            'users' => $users
        ]);
    }

    // إضافة مستخدم جديد (Admin Only)
    public function store(Request $request)
    {
        if (auth()->user()->user_type !== 'admin') {
            return response()->json(['status' => false, 'message' => 'غير مصرح لك'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:20|unique:users,phone',
            'governorate' => 'nullable|string|max:255',
            'user_type' => 'required|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'governorate' => $validated['governorate'] ?? null,
            'user_type' => $validated['user_type'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة المستخدم بنجاح',
            'user' => $user,
        ], 201);
    }

    // تعديل بيانات مستخدم (Admin Only)
    public function update(Request $request, $id)
    {
        // if (auth()->user()->user_type !== 'admin') {
        //     return response()->json(['status' => false, 'message' => 'غير مصرح لك'], 403);
        // }

        $user = User::findOrFail($id);
        $wasApproved = $user->is_approved; // حفظ الحالة السابقة

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|required|string|max:20|unique:users,phone,' . $user->id,
            'governorate' => 'nullable|string|max:255',
            'user_type' => 'sometimes|required|string',
            'is_approved' => 'sometimes|in:0,1',
            'the_best' => 'sometimes|in:0,1',

        ]);

        $user->update($validated);

        // إرسال إشعار عند قبول الحساب لأول مرة
        if (isset($validated['is_approved']) && $validated['is_approved'] == 1 && !$wasApproved) {
            Notifier::send(
                $user,
                'account_approved',
                'تم قبول حسابك',
                'مبروك! تم قبول حسابك من قبل الإدارة. يمكنك الآن الاستفادة من جميع خدمات التطبيق.'
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث بيانات المستخدم بنجاح',
            'user' => $user,
        ]);
    }

    // حذف مستخدم (Admin Only)
    public function destroy($id)
    {
        if (auth()->user()->user_type !== 'admin') {
            return response()->json(['status' => false, 'message' => 'غير مصرح لك'], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف المستخدم بنجاح',
        ]);
    }

    // تغيير كلمة المرور للمستخدم المسجل دخوله
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        // التحقق من كلمة المرور الحالية
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'كلمة المرور الحالية غير صحيحة',
            ], 400);
        }

        // تحديث كلمة المرور
        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        // إلغاء جميع الـ tokens الحالية لإجبار المستخدم على تسجيل الدخول مرة أخرى
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح. يرجى تسجيل الدخول مرة أخرى.',
        ]);
    }
}
