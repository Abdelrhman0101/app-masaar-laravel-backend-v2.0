<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('menu_items', function ($table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('section_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('restaurant_id')->references('id')->on('restaurant_details')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('menu_sections')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_items');
    }

};
