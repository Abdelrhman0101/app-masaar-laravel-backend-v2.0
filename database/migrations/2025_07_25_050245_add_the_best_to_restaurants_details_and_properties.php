<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('restaurant_details', function ($table) {
            $table->boolean('the_best')->default(false)->after('working_hours');
        });

        Schema::table('properties', function ($table) {
            $table->boolean('the_best')->default(false)->after('is_ready');
        });
    }

    public function down()
    {
        Schema::table('restaurant_details', function ($table) {
            $table->dropColumn('the_best');
        });

        Schema::table('properties', function ($table) {
            $table->dropColumn('the_best');
        });
    }

};
