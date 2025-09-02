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
        Schema::create('restaurant_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('profile_image');
            $table->string('restaurant_name');
            $table->string('logo_image');
            $table->string('owner_id_front_image');
            $table->string('owner_id_back_image');
            $table->string('license_front_image');
            $table->string('license_back_image');
            $table->string('commercial_register_front_image');
            $table->string('commercial_register_back_image');
            $table->boolean('vat_included')->default(false);
            $table->string('vat_image_front')->nullable();
            $table->string('vat_image_back')->nullable();
            $table->json('cuisine_types');
            $table->json('branches');
            $table->boolean('delivery_available')->default(false);
            $table->decimal('delivery_cost_per_km', 8, 2)->nullable();
            $table->boolean('table_reservation_available')->default(false);
            $table->integer('max_people_per_reservation')->nullable();
            $table->string('reservation_notes')->nullable();
            $table->boolean('deposit_required')->default(false);
            $table->decimal('deposit_amount', 8, 2)->nullable();
            $table->json('working_hours');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_details');
    }
};
