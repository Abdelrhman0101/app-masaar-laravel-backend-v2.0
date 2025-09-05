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
        Schema::table('messages', function (Blueprint $table) {
            // Add new columns for flexible message system (only if they don't exist)
            if (!Schema::hasColumn('messages', 'metadata')) {
                $table->json('metadata')->nullable()->after('read_at');
            }
            if (!Schema::hasColumn('messages', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
        
        // Add indexes in a separate schema call to avoid conflicts
        Schema::table('messages', function (Blueprint $table) {
            // Add indexes for better performance (only if they don't exist)
            if (!Schema::hasIndex('messages', 'messages_conversation_id_created_at_index')) {
                try {
                    $table->index(['conversation_id', 'created_at']);
                } catch (\Exception $e) {
                    // Index might already exist, ignore
                }
            }
            
            // Only add type index if type column exists and index doesn't exist
            if (Schema::hasColumn('messages', 'type') && !Schema::hasIndex('messages', 'messages_type_index')) {
                try {
                    $table->index(['type']);
                } catch (\Exception $e) {
                    // Index might already exist, ignore
                }
            }
            
            // Only add is_read index if is_read column exists and index doesn't exist
            if (Schema::hasColumn('messages', 'is_read') && !Schema::hasIndex('messages', 'messages_is_read_index')) {
                try {
                    $table->index(['is_read']);
                } catch (\Exception $e) {
                    // Index might already exist, ignore
                }
            }
            
            if (!Schema::hasIndex('messages', 'messages_created_at_index')) {
                try {
                    $table->index(['created_at']);
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
        Schema::table('messages', function (Blueprint $table) {
            // Drop indexes that were added (only if they exist)
            if (Schema::hasIndex('messages', 'messages_conversation_id_created_at_index')) {
                try {
                    $table->dropIndex(['conversation_id', 'created_at']);
                } catch (\Exception $e) {
                    // Index might not exist, ignore
                }
            }
            
            // Only drop type index if type column and index exist
            if (Schema::hasColumn('messages', 'type') && Schema::hasIndex('messages', 'messages_type_index')) {
                try {
                    $table->dropIndex(['type']);
                } catch (\Exception $e) {
                    // Index might not exist, ignore
                }
            }
            
            // Only drop is_read index if is_read column and index exist
            if (Schema::hasColumn('messages', 'is_read') && Schema::hasIndex('messages', 'messages_is_read_index')) {
                try {
                    $table->dropIndex(['is_read']);
                } catch (\Exception $e) {
                    // Index might not exist, ignore
                }
            }
            
            if (Schema::hasIndex('messages', 'messages_created_at_index')) {
                try {
                    $table->dropIndex(['created_at']);
                } catch (\Exception $e) {
                    // Index might not exist, ignore
                }
            }
            
            // Drop columns that were added (not the existing ones)
            $table->dropColumn([
                'metadata',
                'deleted_at'
            ]);
        });
    }
};