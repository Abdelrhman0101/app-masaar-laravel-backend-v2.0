<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('restaurant_banners', function (Blueprint $table) {
        $table->id();
        $table->string('image_url'); // لتخزين رابط الصورة
        $table->unsignedInteger('position')->default(0); // للتحكم في ترتيب ظهور البنرات
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_banners');
    }
};
