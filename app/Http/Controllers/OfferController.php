<?php

// app/Http/Controllers/OfferController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    // إضافة عرض جديد
    public function store(Request $request)
    {
        $request->validate([
            'service_request_id' => 'required|exists:service_requests,id',
            'offer_price'        => 'required|numeric|min:0',
            'notes'              => 'nullable|string',
        ]);

        $user = Auth::user();

        // تحديد نوع مقدم الخدمة بناءً على user_type
        $providerType = $user->user_type; // "car_rental_office" أو "driver"

        // التأكد من أن الطلب مفتوح لاستقبال العروض (اختياري لو عندك حالات للطلبات)
        $serviceRequest = ServiceRequest::findOrFail($request->service_request_id);
        // ممكن تضيف شرط أن الطلب status = 'pending' فقط

        $offer = Offer::create([
            'service_request_id' => $request->service_request_id,
            'provider_id'        => $user->id,
            'provider_type'      => $providerType,
            'offer_price'        => $request->offer_price,
            'notes'              => $request->notes,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'تم تقديم العرض بنجاح',
            'offer'   => $offer
        ]);
    }
}
