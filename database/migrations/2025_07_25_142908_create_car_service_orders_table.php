<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_service_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('car_rental_id'); // ممكن تبقى لمكتب أو سائق
            $table->unsignedBigInteger('provider_id')->nullable(); // يتم تعيينه بعد القبول
            $table->enum('order_type', ['rent', 'ride']);
            $table->string('car_category')->nullable();
            $table->enum('payment_method', ['cash', 'bank_transfer'])->default('cash');
            $table->enum('status', ['pending_admin', 'pending_provider', 'negotiation', 'accepted', 'started', 'finished', 'rejected', 'cancelled'])->default('pending_admin');
            $table->decimal('requested_price', 10, 2)->nullable();
            $table->decimal('agreed_price', 10, 2)->nullable();
            $table->string('from_location');
            $table->string('to_location');
            $table->timestamp('delivery_time')->nullable();
            $table->timestamp('requested_date')->nullable();
            $table->timestamp('provider_offer_date')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('car_rental_id')->references('id')->on('car_rentals')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_service_orders');
    }
};
