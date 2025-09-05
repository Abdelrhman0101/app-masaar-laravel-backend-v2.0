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
        Schema::table('conversations', function (Blueprint $table) {
            // Add new columns for flexible conversation system
            $table->unsignedBigInteger('user1_id')->nullable()->after('id');
            $table->unsignedBigInteger('user2_id')->nullable()->after('user1_id');
            $table->enum('type', ['user_user', 'admin_user', 'provider_user'])->default('user_user')->after('user2_id');
            $table->string('title')->nullable()->after('type');
            $table->timestamp('last_message_at')->nullable()->after('title');
            $table->json('metadata')->nullable()->after('last_message_at');
            $table->softDeletes()->after('updated_at');
            
            // Add indexes for better performance
            $table->index(['user1_id', 'user2_id']);
            $table->index(['type']);
            $table->index(['status']);
            $table->index(['last_message_at']);
            
            // Add foreign key constraints
            $table->foreign('user1_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user2_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['user1_id']);
            $table->dropForeign(['user2_id']);
            
            // Drop indexes
            $table->dropIndex(['user1_id', 'user2_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['last_message_at']);
            
            // Drop columns
            $table->dropColumn([
                'user1_id',
                'user2_id', 
                'type',
                'title',
                'last_message_at',
                'metadata',
                'deleted_at'
            ]);
        });
    }
};