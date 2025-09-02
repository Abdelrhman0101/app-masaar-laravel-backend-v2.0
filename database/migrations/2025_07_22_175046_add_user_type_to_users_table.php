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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', [
                'normal',
                'real_estate_office',
                'real_estate_individual',
                'restaurant',
                'car_rental_office',
                'driver'
            ])->after('password');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(){
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_type');
        });
    }
};
