<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRequest;

class ServiceRequestController extends Controller
{
    // إنشاء طلب جديد
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'type' => 'required|in:delivery,rent',
            'request_data' => 'required|array',
        ]);
        $requestData = $request->request_data;

        // تحديد القيم الافتراضية حسب نوع الطلب
        if ($request->type === 'delivery') {
            $approved_by_admin = true;
            $status = 'approved';
        } else {
            $approved_by_admin = false;
            $status = 'pending';
        }

        $serviceRequest = ServiceRequest::create([
            'user_id'           => $user->id,
            'governorate'       => $user->governorate,
            'type'              => $request->type,
            'request_data'      => $requestData,
            'approved_by_admin' => $approved_by_admin,
            'status'            => $status,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الطلب بنجاح',
            'data' => $serviceRequest
        ]);
    }

    // عرض الطلبات الخاصة بالمستخدم الحالي
    public function index()
    {
        $requests = ServiceRequest::where('user_id', auth()->id())
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $requests
        ]);
    }
}
