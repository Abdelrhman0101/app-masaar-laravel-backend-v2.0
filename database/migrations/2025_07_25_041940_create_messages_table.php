<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            
            // Sender information (nullable to support system messages)
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // Message content and type
            $table->text('content');
            $table->string('type', 50)->default('text');
            
            // Read status and metadata
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('metadata')->nullable();
            
            // Soft deletes and timestamps
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id']);
            $table->index(['type']);
            $table->index(['is_read']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};