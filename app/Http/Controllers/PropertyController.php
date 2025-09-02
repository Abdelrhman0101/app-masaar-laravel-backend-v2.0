<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\RealEstate;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    // عرض كل عقارات مقدم الخدمة (مع إمكانية فلترة بالـ real_estate_id)
public function index(Request $request)
{
    $user = $request->user();

    // جلب العقارات للمستخدم الحالي فقط
    $properties = Property::where('user_id', $user->id)->get();

    return response()->json(['status' => true, 'properties' => $properties]);
}

    // إضافة عقار جديد
    public function store(Request $request)
    {
        $user = $request->user();

        // استخراج real_estate_id من علاقة المستخدم
        $realEstate = RealEstate::where('user_id', $user->id)->first();

        if (!$realEstate) {
            return response()->json([
                'status' => false,
                'message' => 'هذا المستخدم لا يمتلك حساب عقاري.'
            ], 403);
        }

        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'view' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'area' => 'nullable|string',
            'submitted_by' => 'nullable|string',
            'submitted_price' => 'nullable|string',
            'is_ready' => 'boolean',
            'the_best' => 'sometimes|in:0,1',

        ]);
    $validated['real_estate_id'] = $realEstate->id;
    $validated['user_id'] = $user->id; // <-- الجديد
    $property = Property::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة العقار بنجاح',
            'property' => $property
        ], 201);
    }


    // تعديل بيانات عقار
    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $validated = $request->validate([
            'address' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'view' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'area' => 'nullable|string',
            'submitted_by' => 'nullable|string',
            'submitted_price' => 'nullable|string',
            'is_ready' => 'boolean',
            'the_best' => 'sometimes|in:0,1',

        ]);

        $property->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث العقار بنجاح',
            'property' => $property
        ]);
    }

    // حذف عقار
    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف العقار بنجاح'
        ]);
    }

    public function updateTheBest(Request $request, $id)
    {
        // التأكد من الصلاحية (مثلاً بوسيط is_admin أو Gate أو Policy)
        $this->authorize('is_admin');

        $property = Property::findOrFail($id);
        $property->validate([
            'the_best' => 'required|boolean',
        ]);

        $property->the_best = $request->the_best;
        $property->save();

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث وضع الأفضل',
            'restaurant' => $property
        ]);
    }
    // PropertyController.php

public function allProperties()
{
    $properties = Property::with(['realEstate', 'appointments']) // لو عايز العلاقات
        ->latest()
        ->get();

    return response()->json([
        'status' => true,
        'properties' => $properties
    ]);
}

}
