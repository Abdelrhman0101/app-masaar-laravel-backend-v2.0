<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('notifications', function (Blueprint $table) {
            $table->json('data')->nullable()->after('message'); // تفاصيل إضافية
            $table->index(['user_id', 'is_read', 'created_at']);
        });
    }
    public function down(): void {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('data');
            $table->dropIndex(['user_id', 'is_read', 'created_at']);
        });
    }
};
