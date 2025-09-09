<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestaurantBanner;
use Illuminate\Http\Request;

class RestaurantBannerController extends Controller
{
    /**
     * جلب قائمة بكل البنرات مع البيانات الكاملة.
     * GET /api/restaurant-banners
     */
    public function index()
    {
        // جلب البنرات مع البيانات الكاملة مرتبة حسب حقل الترتيب
        $banners = RestaurantBanner::orderBy('position')->get(['id', 'image_url', 'position']);

        // إرجاع الرد بالتنسيق الصحيح
        return response()->json([
            'ResturantBanners' => $banners
        ]);
    }

    /**
     * إضافة بانر جديد.
     * POST /api/restaurant-banners
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image_url' => 'required|string|max:255',
            'position' => 'nullable|integer',
        ]);

        $banner = RestaurantBanner::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'تمت إضافة البانر بنجاح!',
            'banner' => $banner,
        ], 201);
    }

    /**
     * حذف بانر معين.
     * DELETE /api/restaurant-banners/{banner}
     */
    public function destroy(RestaurantBanner $banner)
    {
        // نستخدم Route Model Binding هنا ليسهل الوصول للبانر
        $banner->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف البانر بنجاح.',
        ]);
    }
}