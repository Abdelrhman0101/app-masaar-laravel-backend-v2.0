<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_cars_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_rental_id'); // يربطها بجدول car_rentals (مكتب أو سائق)
            $table->enum('owner_type', ['office', 'driver']); // نوع المالك الحالي
            $table->string('license_front_image');
            $table->string('license_back_image');
            $table->string('car_license_front');
            $table->string('car_license_back');
            $table->string('car_image_front');
            $table->string('car_image_back');
            $table->string('car_type');
            $table->string('car_model');
            $table->string('car_color')->nullable();
            $table->string('car_plate_number');
            $table->timestamps();

            $table->foreign('car_rental_id')->references('id')->on('car_rentals')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cars');
    }
};
