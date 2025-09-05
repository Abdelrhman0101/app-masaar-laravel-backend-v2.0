<?php

namespace App\Jobs;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupConversationData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->cleanupOldMessages();
            $this->cleanupArchivedConversations();
            $this->cleanupTypingIndicators();
            $this->cleanupUserStatusCache();
            
            Log::info('Conversation data cleanup completed successfully');
        } catch (\Exception $e) {
            Log::error('Conversation data cleanup failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Clean up old soft-deleted messages
     */
    private function cleanupOldMessages(): void
    {
        $daysToKeep = config('conversation.cleanup.soft_deleted_messages_days', 30);
        $cutoffDate = Carbon::now()->subDays($daysToKeep);
        
        $deletedCount = Message::onlyTrashed()
            ->where('deleted_at', '<', $cutoffDate)
            ->forceDelete();
            
        Log::info("Permanently deleted {$deletedCount} old messages");
    }

    /**
     * Clean up old archived conversations
     */
    private function cleanupArchivedConversations(): void
    {
        $daysToKeep = config('conversation.cleanup.archived_conversations_days', 90);
        $cutoffDate = Carbon::now()->subDays($daysToKeep);
        
        $archivedConversations = Conversation::where('status', 'archived')
            ->where('updated_at', '<', $cutoffDate)
            ->get();
            
        foreach ($archivedConversations as $conversation) {
            // Delete all messages in this conversation
            $conversation->messages()->forceDelete();
            
            // Delete the conversation
            $conversation->forceDelete();
        }
        
        Log::info("Permanently deleted {$archivedConversations->count()} old archived conversations");
    }

    /**
     * Clean up expired typing indicators
     */
    private function cleanupTypingIndicators(): void
    {
        $pattern = 'typing.*';
        $keys = Cache::getRedis()->keys($pattern);
        
        $expiredCount = 0;
        foreach ($keys as $key) {
            // Remove the Redis prefix if it exists
            $cleanKey = str_replace(config('database.redis.options.prefix', ''), '', $key);
            
            if (!Cache::has($cleanKey)) {
                $expiredCount++;
            }
        }
        
        Log::info("Cleaned up {$expiredCount} expired typing indicators");
    }

    /**
     * Clean up old user status cache entries
     */
    private function cleanupUserStatusCache(): void
    {
        $pattern = 'user_status.*';
        $keys = Cache::getRedis()->keys($pattern);
        
        $cleanedCount = 0;
        foreach ($keys as $key) {
            // Remove the Redis prefix if it exists
            $cleanKey = str_replace(config('database.redis.options.prefix', ''), '', $key);
            
            $status = Cache::get($cleanKey);
            if ($status && isset($status['last_seen'])) {
                $lastSeen = Carbon::parse($status['last_seen']);
                
                // Remove status if user has been offline for more than 24 hours
                if ($lastSeen->diffInHours(now()) > 24 && $status['status'] === 'offline') {
                    Cache::forget($cleanKey);
                    $cleanedCount++;
                }
            }
        }
        
        Log::info("Cleaned up {$cleanedCount} old user status cache entries");
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return ['cleanup', 'conversation', 'maintenance'];
    }
}