<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminServiceRequestController extends Controller
{
   public function archive(Request $request)
{
    // لو حبيت تدعم فلترة لاحقاً (status/type/user_id...) استقبلهم هنا
    $query = \App\Models\ServiceRequest::with(['user', 'offers'])
        ->orderByDesc('created_at');

    // فلترة حسب الحالة لو محتاج
    if ($request->has('status')) {
        $query->where('status', $request->status);
    }

    $requests = $query->get();

    // لو حبيت ترجع النتائج مجمعة حسب الحالة:
    $grouped = $requests->groupBy('status');

    return response()->json([
        'status' => true,
        'total' => $requests->count(),
        'grouped' => $grouped,
        'all_requests' => $requests, // لو حابب تعرض كل الطلبات مباشرة
    ]);
}

}
