<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;

class AppSettingController extends Controller
{
    // جلب كل الإعدادات
    public function index()
    {
        $settings = AppSetting::all()->pluck('value', 'key');
        // حول القيم من JSON إلى Array عند العرض
        $settings = $settings->map(function($v) {
            $decoded = json_decode($v, true);
            return $decoded ?? $v;
        });
        return response()->json([
            'status' => true,
            'settings' => $settings
        ]);
    }

    // تحديث/إضافة إعداد
    public function update(Request $request, $key)
    {
        $value = $request->input('value');
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        AppSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        return response()->json(['status' => true, 'message' => 'تم تحديث الإعدادات']);
    }
}
