<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            // *** هذا هو التغيير الجوهري ***
            // المحادثة تنتمي للمستخدم (غير المشرف).
            // كل مستخدم له محادثة واحدة فقط مع الإدارة، لذا هذا الحقل فريد.
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            
            // لا حاجة لتحديد أي مشرف هنا. المشرفون يدخلون ويخرجون من المحادثة بحكم صلاحياتهم.

            $table->enum('status', ['open', 'closed'])->default('open')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};