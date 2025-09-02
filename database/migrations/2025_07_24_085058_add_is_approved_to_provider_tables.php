<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('users', function ($table) {
        $table->boolean('is_approved')->default(0)->after('user_type');
    });
}

public function down()
{
    Schema::table('users', function ($table) {
        $table->dropColumn('is_approved');
    });
}

};
