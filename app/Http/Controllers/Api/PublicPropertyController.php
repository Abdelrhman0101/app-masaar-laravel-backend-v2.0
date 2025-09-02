<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use Illuminate\Http\Request;

class PublicPropertyController extends Controller
{
    /**
     * Endpoint ذكي وشامل للبحث عن كل العقارات مع فلاتر اختيارية.
     */
    public function index(Request $request)
    {
        // نبدأ ببناء الاستعلام الأساسي.
        $query = Property::query();

        // **شرط أمني مهم:** جلب العقارات فقط من المستخدمين الموافق عليهم (is_approved).
        $query->whereHas('user', function ($q) {
            $q->where('is_approved', 1);
        });
        
        // -- الفلترة الديناميكية الذكية --

        // فلتر: هل العقار من "الأفضل" (the_best)؟
        $query->when($request->boolean('the_best'), function ($q) {
            return $q->where('the_best', 1);
        });

        // يمكنك إضافة أي فلاتر مستقبلية هنا بنفس الطريقة
        // مثال: فلتر حسب نوع العقار
        $query->when($request->input('type'), function ($q, $type) {
            return $q->where('type', $type);
        });

        // **الأهم لتحسين الأداء:** تحميل العلاقات مسبقًا
        $query->with(['user:id,name,phone', 'realEstate']);
        
        // جلب النتائج مع تقسيمها إلى صفحات وترتيبها بالأحدث
        $properties = $query->latest()->paginate(15)->withQueryString();

        // إرجاع البيانات بعد تنسيقها باستخدام الـ Resource
        return PropertyResource::collection($properties);
    }
}