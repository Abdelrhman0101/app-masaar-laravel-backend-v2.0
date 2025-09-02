<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('order_id');
        $table->unsignedBigInteger('menu_item_id')->nullable();
        $table->string('title');
        $table->integer('quantity');
        $table->decimal('unit_price', 10, 2);
        $table->decimal('total_price', 10, 2);
        $table->string('image')->nullable();
        $table->timestamps();

        $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('set null');
    });
}
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
