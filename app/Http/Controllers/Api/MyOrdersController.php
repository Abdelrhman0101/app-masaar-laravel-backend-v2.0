<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyOrdersController extends Controller
{
/**
* Endpoint مركزي (بناء يدوي) لجلب كل طلبات المستخدم.
*/
public function getAllMyOrders(Request $request)
{
$user = Auth::user();
$allOrders = [];

// 1. جلب طلبات المطاعم
$restaurantOrders = $user->orders()->with('restaurant:id,restaurant_name')->latest()->get();
foreach ($restaurantOrders as $order) {
$allOrders[] = [
'id' => $order->id,
'order_type' => 'restaurant_order',
'order_number' => $order->order_number,
'status' => $order->status,
'date' => $order->created_at->toIso8601String(),
'title' => 'طلب من مطعم: ' . ($order->restaurant?->restaurant_name ?? 'غير محدد'),
'total_price' => (float) $order->total_price,
'details' => $order->loadMissing('items'), // تفاصيل الطلب آمنة هنا
];
}

// 2. جلب طلبات السيارات
$carOrders = $user->carServiceOrders()->latest()->get();
foreach ($carOrders as $order) {
$allOrders[] = [
'id' => $order->id,
'order_type' => 'car_order',
'order_number' => 'CAR-' . $order->id,
'status' => $order->status,
'date' => $order->created_at->toIso8601String(),
'title' => 'طلب توصيل/تأجير سيارة',
'total_price' => null,
'details' => $order,
];
}

// 3. جلب طلبات معاينة العقارات
$appointments = $user->appointments()->with('property:id,address')->latest()->get(); // نختار فقط حقول معينة
foreach ($appointments as $appointment) {
$allOrders[] = [
'id' => $appointment->id,
'order_type' => 'property_appointment',
'order_number' => 'APT-' . $appointment->id,
'status' => $appointment->status,
'date' => $appointment->appointment_date,
'title' => 'طلب معاينة عقار: ' . ($appointment->property?->address ?? 'غير محدد'),
'total_price' => null,
// نعيد فقط الحقول الأساسية بدلاً من كائن كامل لتجنب التكرار
'details' => [
'id' => $appointment->id,
'status' => $appointment->status,
'appointment_datetime' => $appointment->appointment_date,
'property_address' => $appointment->property?->address,
],
];
}

// 4. جلب طلبات التصاريح الأمنية
$securityPermits = $user->securityPermits()->latest()->get();
foreach ($securityPermits as $permit) {
$allOrders[] = [
'id' => $permit->id,
'order_type' => 'security_permit',
'order_number' => 'SEC-' . $permit->id,
'status' => $permit->status,
'date' => $permit->created_at->toIso8601String(),
'title' => 'طلب تصريح أمني للسفر',
'total_price' => 100.00,
'details' => $permit,
];
}

// 5. ترتيب المصفوفة النهائية حسب التاريخ
usort($allOrders, function ($a, $b) {
return strtotime($b['date']) - strtotime($a['date']);
});

// (منطق الفلترة يبقى كما هو لو أردت)

return response()->json([
'status' => true,
'data' => $allOrders,
]);
}
}