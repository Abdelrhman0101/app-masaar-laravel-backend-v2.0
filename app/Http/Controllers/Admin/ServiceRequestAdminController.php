<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;

class ServiceRequestAdminController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'admin') {
            return response()->json(['status' => false, 'message' => 'غير مسموح لك بالوصول.'], 403);
        }

        $requests = ServiceRequest::where('status', 'pending')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'طلبات العملاء الجديدة.',
            'requests' => $requests
        ]);
    }

    public function approve($id)
    {
        $user = auth()->user();
        if ($user->user_type !== 'admin') {
            return response()->json(['status' => false, 'message' => 'غير مسموح لك بالوصول.'], 403);
        }

        $request = ServiceRequest::findOrFail($id);
        $request->approved_by_admin = true;
        $request->status = 'approved';
        $request->save();

        return response()->json([
            'status' => true,
            'message' => 'تمت الموافقة على الطلب.',
            'request' => $request
        ]);
    }

    public function reject($id)
    {
        $user = auth()->user();
        if ($user->user_type !== 'admin') {
            return response()->json(['status' => false, 'message' => 'غير مسموح لك بالوصول.'], 403);
        }

        $request = ServiceRequest::findOrFail($id);
        $request->approved_by_admin = false;
        $request->status = 'rejected';
        $request->save();

        return response()->json([
            'status' => true,
            'message' => 'تم رفض الطلب.',
            'request' => $request
        ]);
    }
}