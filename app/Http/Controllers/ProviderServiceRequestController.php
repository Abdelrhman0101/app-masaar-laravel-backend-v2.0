<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // تصحيح الاستيراد
use App\Models\ServiceRequest;
use App\Models\Offer;

class ProviderServiceRequestController extends Controller
{
    // 1. جلب الطلبات المتاحة لمقدم الخدمة في نفس المحافظة وتمت الموافقة عليها من الإدارة
    public function index()
    {
        $provider = Auth::user();
        $providerType = $provider->user_type;

        // تحديد نوع الطلبات التي يجب عرضها بناءً على نوع مقدم الخدمة
        $requestTypeToFetch = '';
        if ($providerType === 'car_rental_office') {
            $requestTypeToFetch = 'rent'; // المكاتب ترى طلبات الإيجار
        } elseif ($providerType === 'driver') {
            $requestTypeToFetch = 'delivery'; // السائقون يرون طلبات التوصيل
        } else {
            // إذا كان المستخدم ليس مكتبًا أو سائقًا، لا ترجع أي طلبات
            return response()->json(['status' => true, 'requests' => []]);
        }

        // جلب الطلبات المعتمدة من المشرف والتي من النوع المناسب
        $requests = ServiceRequest::with('user:id,name,phone') // جلب بيانات العميل
            ->where('status', 'approved')
            ->where('type', $requestTypeToFetch)
            ->where('governorate', $provider->governorate) // **فلتر إضافي مهم: عرض طلبات نفس المحافظة فقط**
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'requests' => $requests
        ]);
    }

    // 2. قبول الطلب من مقدم الخدمة (بعرض أو بدون)
    public function accept(Request $request, $id)
    {
        $user = auth()->user();
        // Log بيانات المستخدم قبل اتخاذ أي قرار
        \Log::info('Accepting Offer', [
            'user_id'        => $user->id,
            'provider_type'  => $user->user_type,
            'car_rental_id'  => $user->car_rental->id ?? null,
            'driver_id'      => $user->driver->id ?? null,
        ]);

        $serviceRequest = ServiceRequest::where('id', $id)
            ->where('status', 'approved')
            ->whereNull('selected_offer_id')
            ->first();

        if (!$serviceRequest) {
            return response()->json([
                'status' => false,
                'message' => 'الطلب غير متاح أو تم حجزه بالفعل.'
            ], 404);
        }

        // استخراج id مقدم الخدمة الحقيقي
        if ($user->user_type === 'car_rental_office') {
            $providerId = $user->car_rental->id ?? $user->id;
        } elseif ($user->user_type === 'driver') {
            $providerId = $user->driver->id ?? $user->id;
        } else {
            $providerId = $user->id;
        }

        // لو مقدم الخدمة مقدم offer بالفعل
        if ($request->has('offer_id')) {
            $offer = Offer::find($request->offer_id);
            if (!$offer || $offer->service_request_id != $id) {
                return response()->json([
                    'status' => false,
                    'message' => 'العرض غير صالح لهذا الطلب.',
                ], 422);
            }
            // حفظ الطلب في حالة قيد التنفيذ وربطه بالعرض
            $serviceRequest->status = 'in_progress';
            $serviceRequest->selected_offer_id = $offer->id;
            $serviceRequest->save();
            return response()->json([
                'status' => true,
                'message' => 'تم قبول العرض وأصبح الطلب قيد التنفيذ.',
            ]);
        }

        // السيناريو التاني: القبول المباشر بدون Offer
        $offer = Offer::create([
            'service_request_id' => $serviceRequest->id,
            'provider_id'        => $providerId,
            'provider_type'      => $user->user_type,
            'offer_price'        => $serviceRequest->request_data['price'] ?? 0,
            'notes'              => 'قبول الطلب بالسعر الأصلي',
            'status'             => 'accepted',
        ]);

        // Log بيانات الـ offer الجديد
        \Log::info('Created Offer', [
            'offer_id'        => $offer->id,
            'provider_id'     => $offer->provider_id,
            'provider_type'   => $offer->provider_type,
        ]);

        $serviceRequest->status = 'in_progress';
        $serviceRequest->selected_offer_id = $offer->id;
        $serviceRequest->save();

        return response()->json([
            'status' => true,
            'message' => 'تم قبول الطلب مباشرة وأصبح قيد التنفيذ.',
        ]);
    }

    // 3. إنهاء الطلب (لا يتم إلا من مقدم الخدمة الحقيقي فقط)
public function complete($id)
{
    $user = auth()->user();

    $serviceRequest = ServiceRequest::where('id', $id)
        ->where('status', 'in_progress')
        ->whereNotNull('selected_offer_id')
        ->first();

    if (!$serviceRequest) {
        return response()->json([
            'status' => false,
            'message' => 'لا يمكن إنهاء هذا الطلب، قد لا يكون قيد التنفيذ أو ليس مخصصًا لك.',
        ], 404);
    }

    $offer = Offer::find($serviceRequest->selected_offer_id);
    if (!$offer) {
        return response()->json([
            'status' => false,
            'message' => 'لا يوجد عرض مرتبط بهذا الطلب.',
        ], 404);
    }

    $expectedProviderId = $user->id;

    if ($offer->provider_id != $expectedProviderId || $offer->provider_type != $user->user_type) {
        return response()->json([
            'status' => false,
            'message' => 'هذا الطلب ليس مخصصًا لك.',
        ], 403);
    }

    $serviceRequest->status = 'finished'; // ✅ استخدم قيمة موجودة في enum
    $serviceRequest->save();

    return response()->json([
        'status' => true,
        'message' => 'تم إنهاء الطلب بنجاح.',
    ]);
}

// عرض كل الطلبات اللي مقدم الخدمة الحالي عملها Accept (in_progress)
public function acceptedRequests(Request $request)
{
    $user = auth()->user();
    $providerType = $user->user_type;
    $providerId = ($providerType === 'car_rental_office') ? ($user->car_rental->id ?? $user->id)
                 : (($providerType === 'driver') ? ($user->driver->id ?? $user->id) : $user->id);

    // جلب الـ Offers اللي تخص المستخدم وحالتها accepted أو الطلب حالته in_progress
    $offers = \App\Models\Offer::where('provider_id', $providerId)
        ->where('provider_type', $providerType)
        ->whereHas('serviceRequest', function($q){
            $q->where('status', 'in_progress');
        })
        ->pluck('id')->toArray();

    $requests = ServiceRequest::whereIn('selected_offer_id', $offers)
        ->with(['user', 'offers'])
        ->where('status', 'in_progress')
        ->latest()
        ->get();

    return response()->json([
        'status' => true,
        'requests' => $requests
    ]);
}

// عرض كل الطلبات اللي مقدم الخدمة الحالي أنجزها (finished)
public function completedRequests(Request $request)
{
    $user = auth()->user();
    $providerType = $user->user_type;
    $providerId = ($providerType === 'car_rental_office') ? ($user->car_rental->id ?? $user->id)
                 : (($providerType === 'driver') ? ($user->driver->id ?? $user->id) : $user->id);

    $offers = \App\Models\Offer::where('provider_id', $providerId)
        ->where('provider_type', $providerType)
        ->whereHas('serviceRequest', function($q){
            $q->where('status', 'finished');
        })
        ->pluck('id')->toArray();

    $requests = ServiceRequest::whereIn('selected_offer_id', $offers)
        ->with(['user', 'offers'])
        ->where('status', 'finished')
        ->latest()
        ->get();

    return response()->json([
        'status' => true,
        'requests' => $requests
    ]);
}

}
