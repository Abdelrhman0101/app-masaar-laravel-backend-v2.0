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
    Schema::create('properties', function ($table) {
        $table->id();
        $table->unsignedBigInteger('real_estate_id');
        $table->string('address');
        $table->string('type');
        $table->decimal('price', 12, 2);
        $table->text('description')->nullable();
        $table->string('image_url')->nullable(); // أو $table->json('images')->nullable();
        $table->integer('bedrooms')->nullable();
        $table->integer('bathrooms')->nullable();
        $table->string('view')->nullable();
        $table->string('payment_method')->nullable();
        $table->string('area')->nullable();
        $table->string('submitted_by')->nullable();
        $table->string('submitted_price')->nullable();
        $table->boolean('is_ready')->default(0);
        $table->timestamps();

        $table->foreign('real_estate_id')->references('id')->on('real_estates')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
