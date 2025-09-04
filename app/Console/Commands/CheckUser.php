<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUser extends Command
{
    protected $signature = 'user:check {email}';
    protected $description = 'فحص بيانات المستخدم في قاعدة البيانات';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error('المستخدم غير موجود!');
            return 1;
        }

        $this->info('=== بيانات المستخدم ===');
        $this->line('ID: ' . $user->id);
        $this->line('الاسم: ' . $user->name);
        $this->line('البريد الإلكتروني: ' . $user->email);
        $this->line('Google ID: ' . ($user->google_id ?? 'غير محدد'));
        $this->line('نوع تسجيل الدخول: ' . ($user->login_type ?? 'غير محدد'));
        $this->line('نوع المستخدم: ' . $user->user_type);
        $this->line('مُوافق عليه: ' . ($user->is_approved ? 'نعم' : 'لا'));
        $this->line('البريد مُفعل: ' . ($user->is_email_verified ? 'نعم' : 'لا'));
        $this->line('الحساب نشط: ' . ($user->account_active ? 'نعم' : 'لا'));
        $this->line('الصورة الشخصية: ' . ($user->avatar ?? 'غير محددة'));
        $this->line('تاريخ الإنشاء: ' . $user->created_at);
        $this->line('تاريخ التحديث: ' . $user->updated_at);

        return 0;
    }
}