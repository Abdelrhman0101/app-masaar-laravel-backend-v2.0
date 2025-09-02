<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource; // <-- !! أضف هذا السطر !!

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'real_estate_id' => $this->real_estate_id,
            'user_id' => $this->user_id,
            'address' => $this->address,
            'type' => $this->type,
            'price' => $this->price,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'view' => $this->view,
            'payment_method' => $this->payment_method,
            'area' => $this->area,
            'is_ready' => (bool)$this->is_ready,
            'the_best' => (bool)$this->the_best,
            'created_at' => $this->created_at->toDateTimeString(),
            
            // الآن، هذا السطر سيعمل بنجاح لأن UserResource أصبح موجودًا ومعروفًا.
            'provider' => new UserResource($this->whenLoaded('user')),
        ];
    }
}