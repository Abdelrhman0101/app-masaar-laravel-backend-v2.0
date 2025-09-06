<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // أسقط قيد المفتاح الأجنبي أولاً (إن وُجد)
        Schema::table('messages', function (Blueprint $table) {
            try {
                $table->dropForeign(['sender_id']);
            } catch (\Throwable $e) {
                try {
                    // محاولة بديلة باستخدام الاسم الافتراضي
                    $table->dropForeign('messages_sender_id_foreign');
                } catch (\Throwable $e2) {
                    // تجاهل إذا لم يكن موجودًا
                }
            }
        });

        // عدّل العمود ليصبح nullable باستخدام SQL خام لتجنب الحاجة إلى doctrine/dbal
        try {
            DB::statement('ALTER TABLE `messages` MODIFY `sender_id` BIGINT UNSIGNED NULL');
        } catch (\Throwable $e) {
            // لبعض المحركات قد يتطلب النوع "BIGINT(20) UNSIGNED"
            try {
                DB::statement('ALTER TABLE `messages` MODIFY `sender_id` BIGINT(20) UNSIGNED NULL');
            } catch (\Throwable $e2) {
                throw $e2; // ارمِ الاستثناء إن فشل كلاهما
            }
        }

        // أعد ربط المفتاح الأجنبي مع onDelete('cascade') كما كان
        Schema::table('messages', function (Blueprint $table) {
            try {
                $table->foreign('sender_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
            } catch (\Throwable $e) {
                // تجاهل إن كان موجودًا بالفعل
            }
        });
    }

    public function down(): void
    {
        // أسقط قيد المفتاح الأجنبي أولاً
        Schema::table('messages', function (Blueprint $table) {
            try {
                $table->dropForeign(['sender_id']);
            } catch (\Throwable $e) {
                try {
                    $table->dropForeign('messages_sender_id_foreign');
                } catch (\Throwable $e2) {
                    // تجاهل إذا لم يكن موجودًا
                }
            }
        });

        // أعد العمود إلى NOT NULL
        try {
            DB::statement('ALTER TABLE `messages` MODIFY `sender_id` BIGINT UNSIGNED NOT NULL');
        } catch (\Throwable $e) {
            try {
                DB::statement('ALTER TABLE `messages` MODIFY `sender_id` BIGINT(20) UNSIGNED NOT NULL');
            } catch (\Throwable $e2) {
                throw $e2;
            }
        }

        // أعد ربط المفتاح الأجنبي كما كان
        Schema::table('messages', function (Blueprint $table) {
            try {
                $table->foreign('sender_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
            } catch (\Throwable $e) {
                // تجاهل إن كان موجودًا بالفعل
            }
        });
    }
};