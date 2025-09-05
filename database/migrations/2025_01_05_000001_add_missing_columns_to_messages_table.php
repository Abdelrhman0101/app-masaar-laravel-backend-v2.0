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
            // Add missing columns for flexible message system
            if (!Schema::hasColumn('messages', 'type')) {
                $table->string('type', 50)->default('text')->after('content');
            }
            if (!Schema::hasColumn('messages', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('type');
            }
        });
        
        // Add indexes for the new columns
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'type') && !Schema::hasIndex('messages', 'messages_type_index')) {
                try {
                    $table->index(['type']);
                } catch (\Exception $e) {
                    // Index might already exist, ignore
                }
            }
            
            if (Schema::hasColumn('messages', 'is_read') && !Schema::hasIndex('messages', 'messages_is_read_index')) {
                try {
                    $table->index(['is_read']);
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
            // Drop indexes first
            if (Schema::hasIndex('messages', 'messages_type_index')) {
                try {
                    $table->dropIndex(['type']);
                } catch (\Exception $e) {
                    // Index might not exist, ignore
                }
            }
            
            if (Schema::hasIndex('messages', 'messages_is_read_index')) {
                try {
                    $table->dropIndex(['is_read']);
                } catch (\Exception $e) {
                    // Index might not exist, ignore
                }
            }
            
            // Drop columns
            if (Schema::hasColumn('messages', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('messages', 'is_read')) {
                $table->dropColumn('is_read');
            }
        });
    }
};