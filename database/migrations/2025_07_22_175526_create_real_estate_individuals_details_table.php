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
        Schema::create('real_estate_individuals_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('real_estate_id')->unique();
            $table->string('profile_image');
            $table->string('agent_name');
            $table->string('agent_id_front_image');
            $table->string('agent_id_back_image');
            $table->string('tax_card_front_image');
            $table->string('tax_card_back_image');
            $table->timestamps();

            $table->foreign('real_estate_id')->references('id')->on('real_estates')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_individuals_details');
    }
};
