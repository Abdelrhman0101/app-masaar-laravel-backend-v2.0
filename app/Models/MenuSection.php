<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- **مهم:** إضافة استيراد BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // <-- إضافة HasMany

class MenuSection extends Model
{
    protected $fillable = [
        'restaurant_id',
        'title',
    ];

    /**
     * العلاقة: كل قسم قائمة يمتلك العديد من العناصر.
     */
    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'section_id');
    }

    /**
     * !! التعديل المقترح !!
     * العلاقة العكسية: كل قسم قائمة ينتمي إلى مطعم واحد.
     */
    public function restaurantDetail(): BelongsTo
    {
        return $this->belongsTo(RestaurantDetail::class, 'restaurant_id');
    }
}