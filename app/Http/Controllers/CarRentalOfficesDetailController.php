<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CarRentalOfficesDetailController extends Controller
{
    public function updateAvailability(Request $request, $id)
    {
        $request->validate([
            'is_available_for_delivery' => 'boolean',
            'is_available_for_rent' => 'boolean',
        ]);
        $officeDetail = \App\Models\CarRentalOfficesDetail::findOrFail($id);

        // يفضل التحقق أن المستخدم يملك المكتب/لأسباب أمان
        $officeDetail->update($request->only(['is_available_for_delivery', 'is_available_for_rent']));

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث توافر الخدمة بنجاح',
            'data' => $officeDetail
        ]);
    }

}
