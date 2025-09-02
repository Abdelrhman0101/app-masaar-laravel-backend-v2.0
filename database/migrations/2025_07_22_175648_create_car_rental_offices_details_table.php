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
        Schema::create('car_rental_offices_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_rental_id')->unique();
            $table->string('office_name');
            $table->string('logo_image');
            $table->string('commercial_register_front_image');
            $table->string('commercial_register_back_image');
            $table->json('payment_methods');
            $table->json('rental_options');
            $table->decimal('cost_per_km', 8, 2);
            $table->decimal('daily_driver_cost', 8, 2);
            $table->integer('max_km_per_day');
            $table->timestamps();

            $table->foreign('car_rental_id')->references('id')->on('car_rentals')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_rental_offices_details');
    }
};
