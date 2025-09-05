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
            // Add new columns for flexible message system
            $table->enum('type', ['text', 'image', 'file', 'system'])->default('text')->after('content');
            $table->boolean('is_read')->default(false)->after('type');
            $table->timestamp('read_at')->nullable()->after('is_read');
            $table->json('metadata')->nullable()->after('read_at');
            $table->softDeletes()->after('updated_at');
            
            // Add indexes for better performance
            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id']);
            $table->index(['type']);
            $table->index(['is_read']);
            $table->index(['created_at']);
            
            // Add foreign key constraints if they don't exist
            // Note: We'll check if they exist first to avoid errors
            if (!Schema::hasColumn('messages', 'conversation_id_foreign')) {
                $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            }
            if (!Schema::hasColumn('messages', 'sender_id_foreign')) {
                $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['conversation_id', 'created_at']);
            $table->dropIndex(['sender_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['is_read']);
            $table->dropIndex(['created_at']);
            
            // Drop columns
            $table->dropColumn([
                'type',
                'is_read',
                'read_at',
                'metadata',
                'deleted_at'
            ]);
        });
    }
};