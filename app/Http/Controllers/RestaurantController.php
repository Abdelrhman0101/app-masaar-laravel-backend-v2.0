<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function updateTheBest(Request $request, $id)
    {
        $this->authorize('is_admin'); // تأكد أنك عامل ميدل وير أو صلاحية للأدمن فقط

        $restaurant = RestaurantDetail::findOrFail($id);

        $request->validate([
            'the_best' => 'required|boolean',
        ]);

        $restaurant->the_best = $request->the_best;
        $restaurant->save();

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث وضع الأفضل',
            'restaurant' => $restaurant
        ]);
    }

}
