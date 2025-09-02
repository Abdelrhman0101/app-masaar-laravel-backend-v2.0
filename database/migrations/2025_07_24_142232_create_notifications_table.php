<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('notifications', function ($table) {
        $table->id();
        $table->unsignedBigInteger('user_id'); // المستخدم صاحب الإشعار
        $table->string('type')->nullable(); // نوع الإشعار (account/appointment/admin...)
        $table->string('title')->nullable();
        $table->text('message'); // نص الإشعار
        $table->boolean('is_read')->default(0); // تم القراءة أو لا
        $table->string('link')->nullable(); // لينك اختياري أو ID مرجعي
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('notifications');
}

};
