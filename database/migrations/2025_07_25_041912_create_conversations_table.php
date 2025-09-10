<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            
            // Support for flexible conversation system
            $table->foreignId('user1_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user2_id')->constrained('users')->onDelete('cascade');
            
            // Conversation type and metadata
            $table->enum('type', ['user_user', 'admin_user', 'provider_user'])->default('user_user');
            $table->string('title')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->json('metadata')->nullable();
            
            // Status and soft deletes
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user1_id', 'user2_id']);
            $table->index(['type']);
            $table->index(['status']);
            $table->index(['last_message_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};