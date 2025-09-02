<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            
            // فقط نحدد من أرسل الرسالة (سواء كان المستخدم أو أحد المشرفين).
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');

            // *** تم حذف `receiver_id` لأنه غير ضروري ***
            
            $table->text('content'); // تم تغيير الاسم من 'message' لـ 'content' ليكون أكثر عمومية
            $table->timestamp('read_at')->nullable(); // بدلاً من 'is_read' للسماح بمعرفة وقت القراءة
            
            // يمكن تجاهل 'is_deleted' في هذه المرحلة للتبسيط
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};