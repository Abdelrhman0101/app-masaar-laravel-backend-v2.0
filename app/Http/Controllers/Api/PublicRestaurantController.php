<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantResource;
use App\Models\User;
use App\Models\MenuSection; // <-- **مهم:** تم استيراد موديل أقسام القائمة
use Illuminate\Http\Request;

class PublicRestaurantController extends Controller
{
    /**
     * Endpoint ذكي وشامل للبحث عن كل المطاعم مع فلاتر اختيارية.
     */
    public function index(Request $request)
    {
        // 1. نبدأ بالاستعلام الأساسي للمطاعم المعتمدة
        $query = User::query()
            ->where('user_type', 'restaurant')
            ->where('is_approved', 1);

        // -- 2. تطبيق الفلاتر الديناميكية بناءً على طلب المستخدم --
        
        // فلتر "الأفضل"
        $query->when($request->boolean('the_best'), fn ($q) => $q->where('the_best', 1));

        // فلتر المحافظة
        $query->when($request->input('governorate'), fn ($q, $value) => $q->where('governorate', $value));

        // فلتر نوع المطبخ (Cuisine Type)
        $query->when($request->input('cuisine'), fn ($q, $value) => 
            $q->whereHas('restaurantDetail', fn ($rd) => $rd->whereJsonContains('cuisine_types', $value))
        );

        // فلتر البحث بالاسم
        $query->when($request->input('name'), function ($q, $name) {
            $searchTerm = '%' . $name . '%';
            return $q->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('name', 'like', $searchTerm)
                         ->orWhereHas('restaurantDetail', fn ($rd) => $rd->where('restaurant_name', 'like', $searchTerm));
            });
        });

        // --- !! الإضافة الجديدة والأساسية: الفلتر حسب قسم القائمة !! ---
        $query->when($request->input('menu_section'), function ($q, $sectionTitle) {
            // `whereHas` ستقوم بفلترة المستخدمين (المطاعم) بناءً على وجود علاقة متطابقة
            // `restaurantDetail.menuSections` هي العلاقة المتسلسلة التي عرفناها في الموديلات
            return $q->whereHas('restaurantDetail.menuSections', function ($msQuery) use ($sectionTitle) {
                // الشرط داخل العلاقة: يجب أن يكون عنوان القسم مطابقاً للقيمة المطلوبة
                $msQuery->where('title', $sectionTitle);
            });
        });

        // 3. تحميل العلاقات مسبقاً لتحسين الأداء (Eager Loading)
        $query->with(['restaurantDetail']);
        
        // 4. تنفيذ الاستعلام وجلب النتائج مع تقسيمها لصفحات
        $restaurants = $query->latest()->paginate(15)->withQueryString();

        // 5. إرجاع النتائج بعد تنسيقها عبر الـ Resource
        return RestaurantResource::collection($restaurants);
    }

    /**
     * يعرض تفاصيل مطعم واحد محدد.
     */
    public function show(User $user)
    {
        // هذه الدالة تبقى كما هي تماماً
        // التحقق من نوع المستخدم وحالة الموافقة
        if ($user->user_type !== 'restaurant' || !$user->is_approved) {
            return response()->json(['status' => false, 'message' => 'Restaurant not found or not approved.'], 404);
        }

        $user->load(['restaurantDetail']);
        
        return response()->json([
            'status' => true,
            'data' => new RestaurantResource($user),
        ]);
    }
}