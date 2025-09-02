<?php

namespace App\Http\Resources\MyOrders;

// --- !! التأكد من استيراد PropertyResource !! ---
use App\Http\Resources\PropertyResource; 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            // --- الحقول الموحدة (تبقى كما هي) ---
            'id' => $this->id,
            'order_type' => 'property_appointment',
            'order_number' => 'APT-' . $this->id,
            'status' => $this->status,
            'date' => $this->appointment_date,
            'title' => 'طلب معاينة عقار: ' . $this->property?->address,
            'total_price' => null,

            // --- !! التصحيح النهائي هنا !! ---
            // نحن لا نعيد الموديل الخام، بل نعيد نسخة منسقة وآمنة منه.
            // وهذا يضمن أننا نعيد فقط البيانات التي نريدها وبشكل آمن.
            'details' => [
                'id' => $this->id,
                'customer_id' => $this->customer_id,
                'provider_id' => $this->provider_id,
                'appointment_datetime' => $this->appointment_datetime,
                'note' => $this->note,
                'status' => $this->status,
                'created_at' => $this->created_at->toDateTimeString(),
                
                // هنا نستخدم PropertyResource لعرض العقار المرتبط بشكل آمن
                'property' => new PropertyResource($this->whenLoaded('property')),
            ]
        ];
    }
}