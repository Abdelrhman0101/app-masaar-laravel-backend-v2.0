<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driver_details', function (Blueprint $table) {
            $table->dropColumn([
                'driver_license_front_image',
                'driver_license_back_image',
                'car_license_front_image',
                'car_license_back_image',
                'car_image_front',
                'car_image_back',
                'car_type',
                'car_model',
                'car_color',
                'car_plate_number',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('drivers_details', function (Blueprint $table) {
            $table->string('driver_license_front_image')->nullable();
            $table->string('driver_license_back_image')->nullable();
            $table->string('car_license_front_image')->nullable();
            $table->string('car_license_back_image')->nullable();
            $table->string('car_image_front')->nullable();
            $table->string('car_image_back')->nullable();
            $table->string('car_type')->nullable();
            $table->string('car_model')->nullable();
            $table->string('car_color')->nullable();
            $table->string('car_plate_number')->nullable();
        });
    }
};
