<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::create('menu_sections', function ($table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id');
            $table->string('title');
            $table->timestamps();

            $table->foreign('restaurant_id')->references('id')->on('restaurant_details')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_sections');
    }

};
