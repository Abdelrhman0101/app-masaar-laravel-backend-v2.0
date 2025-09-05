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
            // Add new columns for flexible conversation system (only if they don't exist)
            if (!Schema::hasColumn('conversations', 'type')) {
                $table->enum('type', ['user_user', 'admin_user', 'provider_user'])->default('user_user')->after('user2_id');
            }
            if (!Schema::hasColumn('conversations', 'title')) {
                $table->string('title')->nullable()->after('type');
            }
            if (!Schema::hasColumn('conversations', 'last_message_at')) {
                $table->timestamp('last_message_at')->nullable()->after('title');
            }
            if (!Schema::hasColumn('conversations', 'metadata')) {
                $table->json('metadata')->nullable()->after('last_message_at');
            }
            if (!Schema::hasColumn('conversations', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
        
        // Add indexes in a separate schema call to avoid conflicts
        Schema::table('conversations', function (Blueprint $table) {
            // Add indexes for better performance
            if (Schema::hasColumn('conversations', 'type')) {
                try {
                    $table->index(['type']);
                } catch (\Exception $e) {
                     // Index might already exist, ignore
                 }
            }
            
            if (Schema::hasColumn('conversations', 'last_message_at')) {
                try {
                    $table->index(['last_message_at']);
                } catch (\Exception $e) {
                     // Index might already exist, ignore
                 }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Drop indexes that were added
            if (Schema::hasColumn('conversations', 'type')) {
                $table->dropIndex(['type']);
            }
            if (Schema::hasColumn('conversations', 'last_message_at')) {
                $table->dropIndex(['last_message_at']);
            }
            
            // Drop columns that were added (not user1_id and user2_id as they already existed)
            $table->dropColumn([
                'type',
                'title',
                'last_message_at',
                'metadata',
                'deleted_at'
            ]);
        });
    }
};