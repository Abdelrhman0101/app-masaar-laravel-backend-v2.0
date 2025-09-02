<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->enum('offered_by', ['provider', 'client']);
            $table->decimal('price', 10, 2)->nullable();
            $table->text('offer_note')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('car_service_orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_offers');
    }
};
