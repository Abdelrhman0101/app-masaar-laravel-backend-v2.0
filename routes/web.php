<?php

use Illuminate\Support\Facades\Route;

// صفحة تسجيل الدخول
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::get('/', function () {
    return view('login');
})->name('login');
// صفحة الداشبورد
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
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