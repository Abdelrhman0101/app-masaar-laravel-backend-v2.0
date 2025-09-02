<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('security_permits', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('travel_date');
            $table->string('nationality');
            $table->integer('people_count');
            $table->string('coming_from');
            $table->string('passport_image');
            $table->string('other_document_image')->nullable();
            $table->enum('status', ['new', 'pending', 'approved', 'rejected', 'expired'])->default('new');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('security_permits');
    }

};
