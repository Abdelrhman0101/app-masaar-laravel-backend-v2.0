<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarRental;
use Illuminate\Http\Request;

class CarController extends Controller
{
    // استعراض جميع العربيات لمقدم خدمة معيّن
    public function index($carRentalId)
    {
         $cars = Car::where('car_rental_id', $carRentalId)
        ->orderBy('id', 'desc') // ترتيب تنازلي من الأحدث للأقدم
        ->get();
    return response()->json([
        'status' => true,
        'cars' => $cars
    ]);
    }

    // إضافة عربية جديدة
    public function store(Request $request)
    {
        $request->validate([
            'car_rental_id' => 'required|exists:car_rentals,id',
            'owner_type' => 'required|in:office,driver',
            'license_front_image' => 'required|string',
            'license_back_image' => 'required|string',
            'car_license_front' => 'required|string',
            'car_license_back' => 'required|string',
            'car_image_front' => 'required|string',
            'car_image_back' => 'required|string',
            'car_type' => 'required|string',
            'car_model' => 'required|string',
            'car_color' => 'nullable|string',
            'car_plate_number' => 'required|string',
        ]);

        $car = Car::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة العربية بنجاح',
            'car' => $car
        ]);
    }

    // تحديث عربية
    public function update(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        $car->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث بيانات العربية بنجاح',
            'car' => $car
        ]);
    }

    // حذف عربية
    public function destroy($id)
    {
        $car = Car::findOrFail($id);
        $car->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف العربية بنجاح'
        ]);
    }

    // إرجاع تفاصيل عربية واحدة حسب الـ id
    public function show($id)
    {
        $car = Car::find($id);
        if (!$car) {
            return response()->json([
                'status' => false,
                'message' => 'العربية غير موجودة'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'car' => $car
        ]);
    }

}
