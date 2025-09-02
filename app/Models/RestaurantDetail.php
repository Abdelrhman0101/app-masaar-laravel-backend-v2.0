<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- **مهم:** إضافة استيراد HasMany

class RestaurantDetail extends Model
{
    protected $table = 'restaurant_details';
    
    protected $fillable = [
        'user_id', 'profile_image', 'restaurant_name', 'logo_image', 
        'owner_id_front_image', 'owner_id_back_image', 'license_front_image', 
        'license_back_image', 'commercial_register_front_image', 
        'commercial_register_back_image', 'vat_included', 'vat_image_front', 
        'vat_image_back', 'cuisine_types', 'branches', 'delivery_available', 
        'delivery_cost_per_km', 'table_reservation_available', 
        'max_people_per_reservation', 'reservation_notes', 'deposit_required', 
        'deposit_amount', 'working_hours', 'the_best', 'is_available_for_orders'
    ];

    protected $casts = [
        'cuisine_types' => 'array',
        'branches' => 'array',
        'working_hours' => 'array',
    ];

    /**
     * تعريف العلاقة العكسية مع المستخدم.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * !! التعديل الأساسي !!
     * تعريف العلاقة بين تفاصيل المطعم وأقسام القائمة الخاصة به.
     * هذا ضروري لمنطق الفلترة الجديد.
     */
    public function menuSections(): HasMany
    {
        // افترض أن جدول 'menu_sections' يحتوي على عمود 'restaurant_id'
        // الذي يشير إلى الـ id الخاص بجدول 'restaurant_details'.
        return $this->hasMany(MenuSection::class, 'restaurant_id');
    }
}