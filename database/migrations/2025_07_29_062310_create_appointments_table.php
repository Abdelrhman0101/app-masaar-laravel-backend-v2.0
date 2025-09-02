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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('customer_id'); // العميل
            $table->unsignedBigInteger('provider_id'); // مقدم الخدمة
            $table->dateTime('appointment_datetime');
            $table->text('note')->nullable();
            $table->text('admin_note')->nullable();
            $table->text('provider_note')->nullable();
            $table->enum('status', ['pending', 'admin_approved', 'provider_approved', 'rejected'])->default('pending');
            $table->enum('last_action_by', ['customer', 'admin', 'provider'])->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
