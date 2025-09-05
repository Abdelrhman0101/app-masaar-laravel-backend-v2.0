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
            // Add indexes for better performance
            try {
                $table->index(['conversation_id', 'created_at']);
            } catch (\Exception $e) {
                // Index might already exist, ignore
            }
            
            try {
                $table->index(['type']);
            } catch (\Exception $e) {
                // Index might already exist, ignore
            }
            
            try {
                $table->index(['is_read']);
            } catch (\Exception $e) {
                // Index might already exist, ignore
            }
            
            try {
                $table->index(['created_at']);
            } catch (\Exception $e) {
                // Index might already exist, ignore
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop indexes that were added
            try {
                $table->dropIndex(['conversation_id', 'created_at']);
            } catch (\Exception $e) {
                // Index might not exist, ignore
            }
            
            try {
                $table->dropIndex(['type']);
            } catch (\Exception $e) {
                // Index might not exist, ignore
            }
            
            try {
                $table->dropIndex(['is_read']);
            } catch (\Exception $e) {
                // Index might not exist, ignore
            }
            
            try {
                $table->dropIndex(['created_at']);
            } catch (\Exception $e) {
                // Index might not exist, ignore
            }
            
            // Drop columns that were added (not the existing ones)
            $table->dropColumn([
                'metadata',
                'deleted_at'
            ]);
        });
    }
};