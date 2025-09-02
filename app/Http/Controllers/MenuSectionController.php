<?php

namespace App\Http\Controllers;

use App\Models\MenuSection;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception; // تأكد من استيراد Exception

class MenuSectionController extends Controller
{
    /**
     * استعراض كل الأقسام الخاصة بمطعم مع الوجبات بداخل كل قسم.
     */
    public function index($restaurantId)
    {
        try {
            $sections = MenuSection::where('restaurant_id', $restaurantId)
                ->with('items') // يفترض وجود علاقة اسمها items في الموديل
                ->get();

            return response()->json([
                'status' => true,
                'sections' => $sections
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching sections.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إضافة قسم جديد.
     */
    public function store(Request $request)
    {
        try {
            // التحقق من صحة البيانات المدخلة
            $validatedData = $request->validate([
                'restaurant_id' => 'required|integer|exists:restaurant_details,id',
                'title' => 'required|string|max:255',
            ]);

            // إنشاء القسم الجديد
            $section = MenuSection::create($validatedData);

            // إرجاع استجابة نجاح مع بيانات القسم الجديد
            return response()->json(['status' => true, 'section' => $section], 201); // 201 Created

        } catch (ValidationException $e) {
            // إرجاع أخطاء التحقق من الصحة بشكل واضح
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            // إرجاع أي خطأ آخر يحدث أثناء عملية الحفظ
            return response()->json([
                'status' => false,
                'message' => 'Failed to create section.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تعديل قسم موجود.
     */
    public function update(Request $request, $id)
    {
        try {
            $section = MenuSection::findOrFail($id);

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
            ]);

            $section->update($validatedData);

            return response()->json(['status' => true, 'section' => $section], 200);

        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to update section.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * حذف قسم.
     */
    public function destroy($id)
    {
        try {
            $section = MenuSection::findOrFail($id);
            
            // يمكنك إضافة تحقق هنا للتأكد من أن القسم فارغ قبل حذفه إذا أردت
            if ($section->items()->count() > 0) {
                return response()->json(['status' => false, 'message' => 'Cannot delete section because it contains items.'], 409); // 409 Conflict
            }

            $section->delete();

            return response()->json(['status' => true, 'message' => 'Section deleted successfully.'], 200);

        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to delete section.', 'error' => $e->getMessage()], 500);
        }
    }
}