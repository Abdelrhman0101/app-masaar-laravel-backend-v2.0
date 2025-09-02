<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestaurantBanner;
use Illuminate\Http\Request;

class RestaurantBannerController extends Controller
{
    /**
     * جلب قائمة بكل روابط البنرات.
     * GET /api/restaurant-banners
     */
    public function index()
    {
        // جلب الروابط فقط مرتبة حسب حقل الترتيب
        $bannerUrls = RestaurantBanner::orderBy('position')->pluck('image_url');

        // إرجاع الرد بنفس التنسيق الذي طلبته
        return response()->json([
            'ResturantBanners' => $bannerUrls
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