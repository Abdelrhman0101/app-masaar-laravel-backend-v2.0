<?php
namespace App\Http\Resources\MyOrders;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantOrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_type' => 'restaurant_order', // نوع الطلب لتمييزه في التطبيق
            'order_number' => $this->order_number,
            'status' => $this->status, // الحالة (pending, accepted, etc.)
            'date' => $this->created_at->toIso8601String(),
            'title' => 'طلب من مطعم: ' . $this->restaurant?->restaurant_name,
            'total_price' => $this->total_price,
            'details' => $this, // إرجاع كامل تفاصيل الطلب
        ];
    }
}