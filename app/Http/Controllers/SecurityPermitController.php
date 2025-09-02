<?php

namespace App\Http\Controllers;

use App\Models\SecurityPermit;
use Illuminate\Http\Request;

class SecurityPermitController extends Controller
{
    // 1. إنشاء طلب تصريح جديد (مستخدم عادي)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'travel_date' => 'required|date',
            'nationality' => 'required|string',
            'people_count' => 'required|integer|min:1',
            'coming_from' => 'required|string',
            'passport_image' => 'required|string', // يكون لينك الصورة المرفوعة
            'other_document_image' => 'nullable|string', // يكون لينك أو null
            'notes' => 'nullable|string',
        ]);

        $permit = SecurityPermit::create([
            'user_id' => auth()->id(),
            ...$validated,
            'status' => 'new'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم تقديم طلب التصريح الأمني بنجاح',
            'permit' => $permit
        ], 201);
    }

    // 2. استعراض كل الطلبات (للأدمن فقط)
    public function index()
    {
        $permits = SecurityPermit::with('user')->latest()->get();
        return response()->json([
            'status' => true,
            'permits' => $permits
        ]);
    }

    // 3. تعديل حالة طلب (للأدمن)
    public function updateStatus(Request $request, $id)
    {
        $permit = SecurityPermit::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:new,pending,approved,rejected,expired',
            'notes' => 'nullable|string',
        ]);

        $permit->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث حالة التصريح الأمني',
            'permit' => $permit
        ]);
    }

    // 4. المستخدم يقدر يشوف طلباته فقط
    public function myPermits()
    {
        $permits = SecurityPermit::where('user_id', auth()->id())->latest()->get();
        return response()->json([
            'status' => true,
            'permits' => $permits
        ]);
    }
    public function allPermits()
{
    $permits = SecurityPermit::with('user')->orderByDesc('id')->get();

    return response()->json([
        'status' => true,
        'total' => $permits->count(),
        'permits' => $permits
    ]);
}
}
