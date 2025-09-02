<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('offers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('service_request_id')->constrained()->onDelete('cascade');
        $table->foreignId('provider_id'); // id السائق أو المكتب من جدول users
        $table->string('provider_type'); // car_rental_office or driver (يفضل نوع الحساب)
        $table->decimal('offer_price', 10, 2);
        $table->text('notes')->nullable();
        $table->enum('status', ['new', 'accepted', 'rejected'])->default('new');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
