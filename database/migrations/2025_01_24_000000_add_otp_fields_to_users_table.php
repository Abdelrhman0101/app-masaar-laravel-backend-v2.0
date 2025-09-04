<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // OTP fields for email verification during registration
            $table->string('email_verification_code')->nullable()->after('email_verified_at');
            $table->timestamp('email_verification_expires_at')->nullable()->after('email_verification_code');
            $table->timestamp('email_verification_sent_at')->nullable()->after('email_verification_expires_at');
            $table->unsignedTinyInteger('email_verification_attempts')->default(0)->after('email_verification_sent_at');
            
            // OTP fields for password reset
            $table->string('password_reset_code')->nullable()->after('email_verification_attempts');
            $table->timestamp('password_reset_expires_at')->nullable()->after('password_reset_code');
            $table->timestamp('password_reset_sent_at')->nullable()->after('password_reset_expires_at');
            $table->unsignedTinyInteger('password_reset_attempts')->default(0)->after('password_reset_sent_at');
            
            // Account status for registration verification
            $table->boolean('is_email_verified')->default(false)->after('password_reset_attempts');
            $table->boolean('account_active')->default(false)->after('is_email_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_verification_code',
                'email_verification_expires_at',
                'email_verification_sent_at',
                'email_verification_attempts',
                'password_reset_code',
                'password_reset_expires_at',
                'password_reset_sent_at',
                'password_reset_attempts',
                'is_email_verified',
                'account_active'
            ]);
        });
    }
};