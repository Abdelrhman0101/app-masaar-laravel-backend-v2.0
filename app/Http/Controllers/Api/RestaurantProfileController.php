<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RestaurantProfileController extends Controller
{
    /**
     * جلب تفاصيل المطعم المسجل دخوله حاليًا.
     * GET /api/restaurant/details
     */
    public function show()
    {
        $user = Auth::user();

        // التحقق من أن المستخدم هو مطعم ولديه تفاصيل مرتبطة
        if ($user->user_type !== 'restaurant' || !$user->restaurantDetail) {
            return response()->json(['status' => false, 'message' => 'غير مصرح لك بالوصول.'], 403);
        }

        // إرجاع بيانات المستخدم مع تفاصيل المطعم المحملة
        return response()->json([
            'status' => true,
            'data' => $user->load('restaurantDetail')
        ]);
    }

    /**
     * تحديث تفاصيل المطعم المسجل دخوله حاليًا.
     * POST /api/restaurant/details/update
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم هو مطعم ولديه تفاصيل مرتبطة
        if ($user->user_type !== 'restaurant' || !$user->restaurantDetail) {
            return response()->json(['status' => false, 'message' => 'غير مصرح لك بالوصول.'], 403);
        }

        $validatedData = $request->validate([
            // بيانات المستخدم الأساسية
            'name' => 'sometimes|string|max:255',
            'phone' => ['sometimes', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'governorate' => 'sometimes|string|max:255',
            
            // بيانات تفاصيل المطعم
            'restaurant_name' => 'sometimes|string|max:255',
            'delivery_available' => 'sometimes|boolean',
            'delivery_cost_per_km' => 'nullable|numeric|min:0',
            'table_reservation_available' => 'sometimes|boolean',
            // يمكن إضافة أي حقول أخرى قابلة للتعديل هنا
        ]);

        // استخدام Transaction لضمان حفظ كل التغييرات معًا أو عدم حفظ أي منها
        DB::transaction(function () use ($user, $validatedData) {
            // تحديث البيانات في جدول users
            $user->update($request->only(['name', 'phone', 'governorate']));

            // تحديث البيانات في جدول restaurant_details
            $user->restaurantDetail->update($request->except(['name', 'phone', 'governorate']));
        });

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث بيانات المطعم بنجاح!',
            'data' => $user->fresh()->load('restaurantDetail') // إرجاع البيانات المحدثة
        ]);
    }
}