<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) أضف الأعمدة الجديدة لو مش موجودة
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'user1_id')) {
                $table->unsignedBigInteger('user1_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('conversations', 'user2_id')) {
                $table->unsignedBigInteger('user2_id')->nullable()->after('user1_id');
            }
        });

        // 2) تعبئة الأعمدة الجديدة بناءً على user_id القديم + ADMIN_ID
        $adminId = (int) env('ADMIN_ID', 1);
        // لو الأعمدة موجودة لكن فاضية، ننسخ القيم
        if (Schema::hasColumn('conversations', 'user1_id') && Schema::hasColumn('conversations', 'user2_id')) {
            DB::statement("
                UPDATE conversations
                SET user1_id = COALESCE(user1_id, user_id),
                    user2_id = COALESCE(user2_id, ?)
                WHERE user1_id IS NULL OR user2_id IS NULL
            ", [$adminId]);
        }

        // 3) إضافة العلاقات الخارجية للأعمدة الجديدة (لو لسه مش مضافة)
        try {
            Schema::table('conversations', function (Blueprint $table) {
                // المحاولات محمية بـ try/catch لتفادي التكرار
                try { $table->foreign('user1_id')->references('id')->on('users')->cascadeOnDelete(); } catch (\Throwable $e) {}
                try { $table->foreign('user2_id')->references('id')->on('users')->cascadeOnDelete(); } catch (\Throwable $e) {}
            });
        } catch (\Throwable $e) {}

        // 4) إسقاط الـ FK على user_id ثم حذف العمود القديم إن وجد
        if (Schema::hasColumn('conversations', 'user_id')) {
            // نحدد اسم الـ FK من information_schema إن وجد
            $fk = DB::selectOne("
                SELECT CONSTRAINT_NAME AS name
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'conversations'
                  AND COLUMN_NAME = 'user_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
                LIMIT 1
            ");

            try {
                Schema::table('conversations', function (Blueprint $table) use ($fk) {
                    if ($fk && isset($fk->name)) {
                        $table->dropForeign($fk->name);
                    } else {
                        $table->dropForeign(['user_id']);
                    }
                });
            } catch (\Throwable $e) {
                // كحل أخير: وقف الفحص لإسقاط العمود
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            Schema::table('conversations', function (Blueprint $table) {
                if (Schema::hasColumn('conversations', 'user_id')) {
                    $table->dropColumn('user_id');
                }
            });

            // إعادة التفعيل (لو كنا وقفناه)
            try { DB::statement('SET FOREIGN_KEY_CHECKS=1;'); } catch (\Throwable $e) {}
        }

        // 5) فهرس منع تكرار نفس الثنائي
        try {
            Schema::table('conversations', function (Blueprint $table) {
                $table->unique(['user1_id', 'user2_id'], 'conversations_user_pair_unique');
            });
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        // إزالة الـ unique
        try {
            Schema::table('conversations', function (Blueprint $table) {
                $table->dropUnique('conversations_user_pair_unique');
            });
        } catch (\Throwable $e) {}

        // إعادة user_id (اختياري)
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
        });

        // إسقاط العلاقات والأعمدة الجديدة
        try {
            Schema::table('conversations', function (Blueprint $table) {
                try { $table->dropForeign(['user1_id']); } catch (\Throwable $e) {}
                try { $table->dropForeign(['user2_id']); } catch (\Throwable $e) {}
                if (Schema::hasColumn('conversations', 'user1_id')) $table->dropColumn('user1_id');
                if (Schema::hasColumn('conversations', 'user2_id')) $table->dropColumn('user2_id');
            });
        } catch (\Throwable $e) {}
    }
};
