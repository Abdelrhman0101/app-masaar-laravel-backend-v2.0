<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // بيانات من جدول users
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'profile_image' => $this->profile_image,
            'phone'         => $this->phone,
            'governorate'   => $this->governorate,
            'user_type'     => $this->user_type,
            'is_approved'   => (int) $this->is_approved, // تحويله لرقم لضمان التناسق
            'the_best'      => (int) $this->the_best,
            'created_at'    => $this->created_at->toIso8601String(),
            'updated_at'    => $this->updated_at->toIso8601String(),
            
            // البيانات المرتبطة من جدول تفاصيل المطاعم
            // whenLoaded: للتأكد من أن البيانات تم تحميلها فقط، مما يحسن الأداء
            'restaurant_detail' => new RestaurantDetailResource($this->whenLoaded('restaurantDetail')),
        ];
    }
}