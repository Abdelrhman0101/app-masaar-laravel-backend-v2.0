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
        Schema::create('real_estate_offices_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('real_estate_id')->unique();
            $table->string('office_name');
            $table->string('office_address');
            $table->string('office_phone');
            $table->string('logo_image');
            $table->string('owner_id_front_image');
            $table->string('owner_id_back_image');
            $table->string('office_image');
            $table->string('commercial_register_front_image');
            $table->string('commercial_register_back_image');
            $table->boolean('tax_enabled')->default(false);
            $table->timestamps();

            $table->foreign('real_estate_id')->references('id')->on('real_estates')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_offices_details');
    }
};
