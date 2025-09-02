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
    Schema::create('service_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->enum('type', ['delivery', 'rent']);
        $table->enum('status', ['pending', 'approved', 'rejected', 'in_progress', 'finished'])->default('pending');
        $table->json('request_data'); // نضع هنا كل تفاصيل الطلب (المواصفات)
        $table->boolean('approved_by_admin')->default(false);
        $table->unsignedBigInteger('selected_offer_id')->nullable();
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
