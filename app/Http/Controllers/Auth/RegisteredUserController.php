<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        // 1. التحقق الأساسي من بيانات المستخدم العامة
       $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users',
            'password'    => 'required|string|min:6',
            'phone'       => 'required|string|max:20|unique:users',
            'governorate' => 'nullable|string|max:255',
            'user_type'   => 'required|in:normal,real_estate_office,real_estate_individual,restaurant,car_rental_office,driver,admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // 2. إنشاء المستخدم في جدول users
        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'phone'       => $request->phone,
            'governorate' => $request->governorate,
            'user_type'   => $request->user_type,
        ]);

        // 3. تفريع التسجيل حسب نوع المستخدم
        switch ($user->user_type) {
            case 'normal':
                $user->normalUser()->create([]);
                break;

            case 'real_estate_office':
                $realEstate = $user->realEstate()->create(['type' => 'office']);
                $realEstate->officeDetail()->create([
                    'office_name' => $request->office_name,
                    'office_address' => $request->office_address,
                    'office_phone' => $request->office_phone,
                    'logo_image' => $request->logo_image,
                    'owner_id_front_image' => $request->owner_id_front_image,
                    'owner_id_back_image' => $request->owner_id_back_image,
                    'office_image' => $request->office_image,
                    'commercial_register_front_image' => $request->commercial_register_front_image,
                    'commercial_register_back_image' => $request->commercial_register_back_image,
                    'tax_enabled' => $request->tax_enabled ?? false,
                ]);
                break;

            case 'real_estate_individual':
                $realEstate = $user->realEstate()->create(['type' => 'individual']);
                $realEstate->individualDetail()->create([
                    'profile_image' => $request->profile_image,
                    'agent_name' => $request->agent_name,
                    'agent_id_front_image' => $request->agent_id_front_image,
                    'agent_id_back_image' => $request->agent_id_back_image,
                    'tax_card_front_image' => $request->tax_card_front_image,
                    'tax_card_back_image' => $request->tax_card_back_image,
                ]);
                break;

            case 'restaurant':
                $user->restaurantDetail()->create([
                    'profile_image' => $request->profile_image,
                    'restaurant_name' => $request->restaurant_name,
                    'logo_image' => $request->logo_image,
                    'owner_id_front_image' => $request->owner_id_front_image,
                    'owner_id_back_image' => $request->owner_id_back_image,
                    'license_front_image' => $request->license_front_image,
                    'license_back_image' => $request->license_back_image,
                    'commercial_register_front_image' => $request->commercial_register_front_image,
                    'commercial_register_back_image' => $request->commercial_register_back_image,
                    'vat_included' => $request->vat_included ?? false,
                    'vat_image_front' => $request->vat_image_front,
                    'vat_image_back' => $request->vat_image_back,
                    'cuisine_types' => $request->cuisine_types,
                    'branches' => $request->branches,
                    'delivery_available' => $request->delivery_available ?? false,
                    'delivery_cost_per_km' => $request->delivery_cost_per_km,
                    'table_reservation_available' => $request->table_reservation_available ?? false,
                    'max_people_per_reservation' => $request->max_people_per_reservation,
                    'reservation_notes' => $request->reservation_notes,
                    'deposit_required' => $request->deposit_required ?? false,
                    'deposit_amount' => $request->deposit_amount,
                    'working_hours' => $request->working_hours,
                ]);
                break;

            case 'car_rental_office':
                $carRental = $user->carRental()->create(['rental_type' => 'office']);
                $carRental->officeDetail()->create([
                    'office_name' => $request->office_name,
                    'logo_image' => $request->logo_image,
                    'commercial_register_front_image' => $request->commercial_register_front_image,
                    'commercial_register_back_image' => $request->commercial_register_back_image,
                    'payment_methods' => $request->payment_methods,
                    'rental_options' => $request->rental_options,
                    'cost_per_km' => $request->cost_per_km,
                    'daily_driver_cost' => $request->daily_driver_cost,
                    'max_km_per_day' => $request->max_km_per_day,
                ]);
                break;

            case 'driver':
                $carRental = $user->carRental()->create(['rental_type' => 'driver']);
                $carRental->driverDetail()->create([
                    'profile_image' => $request->profile_image,
                    'payment_methods' => $request->payment_methods,
                    'rental_options' => $request->rental_options,
                    'cost_per_km' => $request->cost_per_km,
                    'daily_driver_cost' => $request->daily_driver_cost,
                    'max_km_per_day' => $request->max_km_per_day,
                    
                ]);
                break;
        }

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'user_id' => $user->id,
            'user_type' => $user->user_type,
        ], 201);

    }

}
