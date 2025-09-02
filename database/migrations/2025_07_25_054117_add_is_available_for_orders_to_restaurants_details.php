<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::table('restaurant_details', function ($table) {
            $table->boolean('is_available_for_orders')->default(false)->after('the_best');
        });
    }

    public function down()
    {
        Schema::table('restaurant_details', function ($table) {
            $table->dropColumn('is_available_for_orders');
        });
    }

};
