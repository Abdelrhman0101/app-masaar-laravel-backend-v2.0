<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Support\Notifier;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    /**
     * إنشاء طلب موعد معاينة جديد.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'date'        => 'required|date_format:Y-m-d H:i',
            'note'        => 'nullable|string|max:1000',
        ]);

        $property = Property::findOrFail($validated['property_id']);

        if (!$property->user_id) {
            return response()->json([
                'status'  => false,
                'message' => 'لا يمكن حجز موعد لهذا العقار لأنه غير مرتبط بمقدم خدمة.',
            ], 422);
        }

        $appointment = Appointment::create([
            'property_id'         => $property->id,
            'customer_id'         => $request->user()->id,
            'provider_id'         => $property->user_id,
            'appointment_datetime'=> $validated['date'],
            'note'                => $validated['note'] ?? null,
            'status'              => 'pending',
            'last_action_by'      => 'customer',
            'updated_by'          => $request->user()->id,
        ]);
        
        // إشعار للأدمن بوجود طلب موعد جديد يحتاج للمراجعة
        try {
            $admins = \App\Models\User::where('user_type', 'admin')->get();
            foreach ($admins as $admin) {
                Notifier::send(
                    $admin, 'new_appointment', 'طلب معاينة جديد',
                    'قدم العميل "' . $request->user()->name . '" طلب معاينة جديد للعقار "' . $property->title . '".',
                    ['appointment_id' => (string)$appointment->id], 'app://admin/appointments/' . $appointment->id
                );
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send new appointment notification to admin: ' . $e->getMessage());
        }

        return response()->json([
            'status'      => true,
            'message'     => 'تم إرسال طلب المعاينة للإدارة.',
            'appointment' => $appointment
        ], 201);
    }

    /**
     * عرض كل المواعيد (للإدارة فقط).
     */
    public function index()
    {
        $appointments = Appointment::with(['property', 'customer', 'provider'])->latest()->get();
        return response()->json([
            'status'      => true,
            'appointments'=> $appointments
        ]);
    }

    /**
     * تحديث حالة الموعد وإرسال الإشعارات المناسبة لكل حالة.
     */
    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $oldStatus = $appointment->status; // نحتفظ بالحالة القديمة للمقارنة

        $validated = $request->validate([
            'status'        => 'required|in:pending,admin_approved,provider_approved,provider_requested_change,rejected,completed',
            'admin_note'    => 'nullable|string|max:1000',
            'provider_note' => 'nullable|string|max:1000',
        ]);
        
        $newStatus = $validated['status'];

        // لا نفعل أي شيء إذا لم تتغير الحالة
        if ($oldStatus === $newStatus) {
            return response()->json([
                'status'      => true,
                'message'     => 'لم تتغير حالة الموعد.',
                'appointment' => $appointment
            ]);
        }
        
        // تحديث الموعد
        $who = 'admin'; /* ... منطق تحديد الفاعل ... */
        $appointment->update($validated + ['last_action_by' => $who, 'updated_by' => $request->user()->id]);

        // إرسال الإشعارات بناءً على الحالة الجديدة
        $this->sendAppointmentNotifications($appointment, $newStatus);
        
        return response()->json([
            'status'      => true,
            'message'     => 'تم تحديث حالة الموعد بنجاح',
            'appointment' => $appointment->fresh()
        ]);
    }
    
    /**
     * دالة مركزية لإرسال الإشعارات المتعلقة بحالة المواعيد.
     */
    private function sendAppointmentNotifications(Appointment $appointment, string $newStatus): void
    {
        $appointment->load(['customer', 'provider', 'property']);
        $customer = $appointment->customer;
        $provider = $appointment->provider;

        try {
            switch ($newStatus) {
                
                case 'admin_approved':
                    if ($customer) Notifier::send($customer, 'appointment_approved', 'تم قبول طلبك!', 'وافقت الإدارة على طلب معاينة العقار "' . $appointment->property->title . '".');
                    if ($provider) Notifier::send($provider, 'new_appointment_request', 'طلب معاينة جديد', 'يوجد طلب معاينة جديد للعقار "' . $appointment->property->title . '" بانتظار موافقتك.');
                    break;

                case 'provider_approved':
                    if ($customer) Notifier::send($customer, 'appointment_confirmed', 'تم تأكيد موعدك!', 'قام مقدم الخدمة بتأكيد موعد معاينة العقار "' . $appointment->property->title . '".');
                    // يمكن إرسال إشعار للأدمن هنا
                    break;

                case 'rejected':
                    $reason = $appointment->admin_note ?? $appointment->provider_note;
                    $rejectionMessage = $reason ? ' السبب: ' . $reason : '';
                    if ($customer) Notifier::send($customer, 'appointment_rejected', 'تم رفض طلبك', 'نأسف، تم رفض طلب معاينة العقار "' . $appointment->property->title . '".' . $rejectionMessage);
                    break;
                
                case 'completed':
                    if ($customer) Notifier::send($customer, 'appointment_completed', 'اكتمال الموعد', 'نتمنى أن تكون معاينتك للعقار "' . $appointment->property->title . '" كانت موفقة. يمكنك الآن تقييم الخدمة.');
                    if ($provider) Notifier::send($provider, 'appointment_completed', 'اكتمال الموعد', 'تم تحديد موعد معاينة العقار "' . $appointment->property->title . '" كمكتمل.');
                    break;
                    
                // يمكنك إضافة حالة provider_requested_change هنا
                // case 'provider_requested_change': ... break;
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send appointment status notifications: ' . $e->getMessage());
        }
    }


    /**
     * عرض المواعيد الخاصة بالمستخدم الحالي (كعميل).
     */
    public function myAppointments(Request $request)
    {
        $appointments = Appointment::with(['property', 'provider'])
            ->where('customer_id', $request->user()->id)
            ->latest()->get();
        return response()->json(['status' => true, 'appointments'=> $appointments]);
    }

    /**
     * عرض المواعيد الخاصة بمقدم الخدمة الحالي.
     */
    public function providerAppointments(Request $request)
    {
        $appointments = Appointment::with(['property', 'customer'])
            ->where('provider_id', $request->user()->id)
            ->latest()->get();
        return response()->json(['status' => true, 'appointments'=> $appointments]);
    }
}