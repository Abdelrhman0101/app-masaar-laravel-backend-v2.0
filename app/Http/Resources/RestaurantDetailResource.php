<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // بيانات من جدول restaurant_details
            'id'                               => $this->id,
            'user_id'                          => $this->user_id,
            'profile_image'                    => $this->profile_image,
            'restaurant_name'                  => $this->restaurant_name,
            'logo_image'                       => $this->logo_image,
            'owner_id_front_image'             => $this->owner_id_front_image,
            'owner_id_back_image'              => $this->owner_id_back_image,
            'license_front_image'              => $this->license_front_image,
            'license_back_image'               => $this->license_back_image,
            'commercial_register_front_image'  => $this->commercial_register_front_image,
            'commercial_register_back_image'   => $this->commercial_register_back_image,
            'vat_included'                     => (int) $this->vat_included,
            'vat_image_front'                  => $this->vat_image_front,
            'vat_image_back'                   => $this->vat_image_back,
            'cuisine_types'                    => $this->cuisine_types,
            'branches'                         => $this->branches,
            'delivery_available'               => (int) $this->delivery_available,
            'delivery_cost_per_km'             => $this->delivery_cost_per_km,
            'table_reservation_available'      => (int) $this->table_reservation_available,
            'max_people_per_reservation'       => $this->max_people_per_reservation,
            'reservation_notes'                => $this->reservation_notes,
            'deposit_required'                 => (int) $this->deposit_required,
            'deposit_amount'                   => $this->deposit_amount,
            'working_hours'                    => $this->working_hours,
            'the_best'                         => (int) $this->the_best, // قد يكون مكررًا، لكن وجوده هنا مفيد
            'is_available_for_orders'          => (int) $this->is_available_for_orders,
        ];
    }
}