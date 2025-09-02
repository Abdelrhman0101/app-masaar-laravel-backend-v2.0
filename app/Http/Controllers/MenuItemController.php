<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    // استعراض كل الوجبات لقسم معين
    public function index($sectionId)
    {
        $items = MenuItem::where('section_id', $sectionId)->get();

        return response()->json([
            'status' => true,
            'items' => $items
        ]);
    }

    // إضافة وجبة جديدة
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurant_details,id',
            'section_id'    => 'required|exists:menu_sections,id',
            'title'         => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'description'   => 'nullable|string',
            'image'         => 'nullable|string', // أو كود رفع الصور إذا فيه رفع
        ]);

        $item = MenuItem::create([
            'restaurant_id' => $request->restaurant_id,
            'section_id'    => $request->section_id,
            'title'         => $request->title,
            'description'   => $request->description,
            'price'         => $request->price,
            'image'         => $request->image,
        ]);

        return response()->json(['status' => true, 'item' => $item]);
    }

    // تعديل وجبة
    public function update(Request $request, $id)
    {
        $item = MenuItem::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image'       => 'nullable|string',
        ]);

        $item->update([
            'title'       => $request->title,
            'description' => $request->description,
            'price'       => $request->price,
            'image'       => $request->image,
        ]);

        return response()->json(['status' => true, 'item' => $item]);
    }

    // حذف وجبة
    public function destroy($id)
    {
        $item = MenuItem::findOrFail($id);
        $item->delete();

        return response()->json(['status' => true, 'message' => 'تم حذف الوجبة بنجاح']);
    }
}
