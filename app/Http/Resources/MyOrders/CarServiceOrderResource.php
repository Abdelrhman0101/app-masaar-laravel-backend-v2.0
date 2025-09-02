<?php
namespace App\Http\Resources\MyOrders;
use Illuminate\Http\Resources\Json\JsonResource;

class CarServiceOrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_type' => 'car_order',
            'order_number' => $this->id, // يمكن استخدام الـ ID كرقم طلب مبدئي
            'status' => $this->status,
            'date' => $this->created_at->toIso8601String(),
            'title' => 'طلب توصيل/تأجير سيارة',
            'total_price' => null, // قد لا يوجد سعر إجمالي في هذه المرحلة
            'details' => $this,
        ];
    }
}