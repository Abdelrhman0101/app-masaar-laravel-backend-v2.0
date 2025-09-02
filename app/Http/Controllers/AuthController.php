<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // تحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users',
            'password'     => 'required|string|min:6',
            'profile_image'=> 'nullable|string|max:255',
            'phone'        => 'required|string|max:20|unique:users',
            'governorate'  => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // إنشاء المستخدم
        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'profile_image' => $request->profile_image,
            'phone'         => $request->phone,
            'governorate'   => $request->governorate,
        ]);

        // إرسال الرد
        return response()->json([
            'status'  => true,
            'message' => 'User registered successfully',
            'user'    => $user,
        ], 201);
    }

}
