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
        Schema::create('driver_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_rental_id')->unique();
            $table->string('profile_image');
            $table->json('payment_methods');
            $table->json('rental_options');
            $table->decimal('cost_per_km', 8, 2);
            $table->decimal('daily_driver_cost', 8, 2);
            $table->integer('max_km_per_day');
            $table->string('driver_license_front_image');
            $table->string('driver_license_back_image');
            $table->string('car_license_front_image');
            $table->string('car_license_back_image');
            $table->string('car_image_front');
            $table->string('car_image_back');
            $table->string('car_type');
            $table->string('car_model');
            $table->string('car_color');
            $table->string('car_plate_number');
            $table->timestamps();

            $table->foreign('car_rental_id')->references('id')->on('car_rentals')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_details');
    }
};
