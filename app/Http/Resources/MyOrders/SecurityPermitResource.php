<?php
namespace App\Http\Resources\MyOrders;
use Illuminate\Http\Resources\Json\JsonResource;

class SecurityPermitResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_type' => 'security_permit',
            'order_number' => $this->id,
            'status' => $this->status,
            'date' => $this->created_at->toIso8601String(),
            'title' => 'طلب تصريح أمني',
            'total_price' => '100.00', // سعر ثابت كما في شاشة الإنشاء
            'details' => $this,
        ];
    }
}