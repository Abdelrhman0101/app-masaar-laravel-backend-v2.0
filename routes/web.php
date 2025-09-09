<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OtpAuthController;
use App\Http\Controllers\Auth\SocialLoginController;

// صفحة تسجيل الدخول
Route::get('/login', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('login');
})->name('login');
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});
// صفحة الداشبورد
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');
// صفحة ادارة الحسابات
Route::get('/accounts', function () {
    return view('accounts');
})->name('accounts')->middleware('auth');
Route::get('/notifications', function () {
    return view('notifications');
})->name('notifications')->middleware('auth');
// 
Route::get('/requests', function () {
    return view('requests');
})->name('requests')->middleware('auth');

Route::get('/securityPermits', function () {
    return view('securityPermits');
})->name('securityPermits')->middleware('auth');

Route::get('/appController', function () {
    return view('appController');
})->name('appController')->middleware('auth');
Route::get('/AppSettings', function () {
    return view('AppSettings');
})->name('AppSettings')->middleware('auth');
// ... (المسارات الأخرى)

// صفحة المحادثات
Route::get('/chat', function () {
    return view('chat'); // اسم ملف ה-Blade سيكون chat.blade.php
})->name('chat')->middleware('auth');

// OTP Web Routes
Route::get('/verify-email', function () {
    return view('auth.verify-email-otp');
})->name('otp.verify-email-form')->middleware('guest');

Route::get('/reset-password', function () {
    return view('auth.reset-password-otp');
})->name('otp.reset-password-form')->middleware('guest');

// OTP Form Handlers
Route::post('/otp/verify-email', [OtpAuthController::class, 'verifyEmailOtp'])->name('otp.verify-email')->middleware('guest');
Route::post('/otp/resend-email-verification', [OtpAuthController::class, 'resendEmailVerificationOtp'])->name('otp.resend-email-verification')->middleware('guest');
Route::post('/otp/send-password-reset', [OtpAuthController::class, 'sendPasswordResetOtp'])->name('otp.send-password-reset')->middleware('guest');
Route::post('/otp/verify-password-reset', [OtpAuthController::class, 'verifyPasswordResetOtp'])->name('otp.verify-password-reset')->middleware('guest');
Route::post('/otp/reset-password', [OtpAuthController::class, 'resetPassword'])->name('otp.reset-password')->middleware('guest');

// Google OAuth Routes
Route::get('auth/google/redirect', [SocialLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback'])->name('google.callback');