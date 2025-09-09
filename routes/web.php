<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OtpAuthController;
use App\Http\Controllers\Auth\SocialLoginController;

// صفحة تسجيل الدخول
Route::get('/login', function () {
    return view('login');
})->name('login')->middleware('guest');
Route::get('/', function () {
    return view('login');
})->name('login')->middleware('guest');
// صفحة الداشبورد
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');
// صفحة ادارة الحسابات
Route::get('/accounts', function () {
    return view('accounts');
})->name('accounts');
Route::get('/notifications', function () {
    return view('notifications');
})->name('notifications');
// 
Route::get('/requests', function () {
    return view('requests');
})->name('requests');

Route::get('/securityPermits', function () {
    return view('securityPermits');
})->name('securityPermits');

Route::get('/appController', function () {
    return view('appController');
})->name('appController');
Route::get('/AppSettings', function () {
    return view('AppSettings');
})->name('AppSettings');
// ... (المسارات الأخرى)

// صفحة المحادثات
Route::get('/chat', function () {
    return view('chat'); // اسم ملف ה-Blade سيكون chat.blade.php
})->name('chat');

// OTP Web Routes
Route::get('/verify-email', function () {
    return view('auth.verify-email-otp');
})->name('otp.verify-email-form');

Route::get('/reset-password', function () {
    return view('auth.reset-password-otp');
})->name('otp.reset-password-form');

// OTP Form Handlers
Route::post('/otp/verify-email', [OtpAuthController::class, 'verifyEmailOtp'])->name('otp.verify-email');
Route::post('/otp/resend-email-verification', [OtpAuthController::class, 'resendEmailVerificationOtp'])->name('otp.resend-email-verification');
Route::post('/otp/send-password-reset', [OtpAuthController::class, 'sendPasswordResetOtp'])->name('otp.send-password-reset');
Route::post('/otp/verify-password-reset', [OtpAuthController::class, 'verifyPasswordResetOtp'])->name('otp.verify-password-reset');
Route::post('/otp/reset-password', [OtpAuthController::class, 'resetPassword'])->name('otp.reset-password');

// Google OAuth Routes
Route::get('auth/google/redirect', [SocialLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback'])->name('google.callback');