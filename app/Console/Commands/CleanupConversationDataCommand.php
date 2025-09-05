<?php

namespace App\Console\Commands;

use App\Jobs\CleanupConversationData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupConversationDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conversation:cleanup 
                            {--force : Force cleanup without confirmation}
                            {--dry-run : Show what would be cleaned without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old conversation data including messages, typing indicators, and user status cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!config('conversation.cleanup.enabled')) {
            $this->error('Conversation cleanup is disabled in configuration.');
            return 1;
        }

        if ($this->option('dry-run')) {
            $this->info('DRY RUN MODE - No actual cleanup will be performed');
            $this->showCleanupStats();
            return 0;
        }

        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to cleanup old conversation data? This action cannot be undone.')) {
                $this->info('Cleanup cancelled.');
                return 0;
            }
        }

        $this->info('Starting conversation data cleanup...');
        
        try {
            // Dispatch the cleanup job
            CleanupConversationData::dispatch();
            
            $this->info('Cleanup job has been dispatched successfully.');
            
            // If running synchronously, show completion message
            if (config('queue.default') === 'sync') {
                $this->info('Cleanup completed successfully.');
            } else {
                $this->info('Cleanup job has been queued. Check the queue worker logs for completion status.');
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Cleanup failed: ' . $e->getMessage());
            Log::error('Conversation cleanup command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Show cleanup statistics without performing actual cleanup
     */
    private function showCleanupStats()
    {
        $this->info('Cleanup Statistics (Dry Run):');
        $this->line('');
        
        // Show what would be cleaned
        $softDeletedDays = config('conversation.cleanup.soft_deleted_messages_days', 30);
        $archivedDays = config('conversation.cleanup.archived_conversations_days', 90);
        
        $this->table(
            ['Category', 'Criteria', 'Action'],
            [
                ['Soft-deleted Messages', "Older than {$softDeletedDays} days", 'Permanently delete'],
                ['Archived Conversations', "Older than {$archivedDays} days", 'Permanently delete with all messages'],
                ['Typing Indicators', 'Expired cache entries', 'Remove from cache'],
                ['User Status Cache', 'Offline users > 24 hours', 'Remove from cache'],
            ]
        );
        
        $this->line('');
        $this->info('Use --force flag to perform actual cleanup without confirmation.');
    }
}