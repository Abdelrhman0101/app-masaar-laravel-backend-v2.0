<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // هنا نختار فقط الحقول التي نريد عرضها في الـ API العام
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'phone' => $this->phone,
            'email' => $this->email, // اختياري
            'profile_image' => $this->profile_image, // اختياري
            'user_type' => $this->user_type, // اختياري
        ];
    }
}