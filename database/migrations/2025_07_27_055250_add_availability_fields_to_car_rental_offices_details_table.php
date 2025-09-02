<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('car_rental_offices_details', function (Blueprint $table) {
            $table->boolean('is_available_for_delivery')->default(true)->after('max_km_per_day');
            $table->boolean('is_available_for_rent')->default(true)->after('is_available_for_delivery');
        });
    }

    public function down()
    {
        Schema::table('car_rental_offices_details', function (Blueprint $table) {
            $table->dropColumn('is_available_for_delivery');
            $table->dropColumn('is_available_for_rent');
        });
    }
};
