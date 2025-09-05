<?php

namespace App\Providers;

use App\Events\NewMessage;
use App\Events\UserStatusChanged;
use App\Events\UserTyping;
use App\Listeners\SendNewMessageNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\CleanupConversationData;

class ConversationServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        NewMessage::class => [
            SendNewMessageNotification::class,
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // Register conversation configuration
        $this->mergeConfigFrom(
            __DIR__.'/../../config/conversation.php',
            'conversation'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register event listeners
        parent::boot();
        
        // Schedule cleanup job if enabled
        if (config('conversation.cleanup.enabled')) {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                
                $cleanupSchedule = config('conversation.cleanup.schedule', 'daily');
                
                switch ($cleanupSchedule) {
                    case 'weekly':
                        $schedule->job(CleanupConversationData::class)->weekly();
                        break;
                    case 'monthly':
                        $schedule->job(CleanupConversationData::class)->monthly();
                        break;
                    case 'daily':
                    default:
                        $schedule->job(CleanupConversationData::class)->daily();
                        break;
                }
            });
        }
        
        // Register broadcasting channels
        $this->registerBroadcastingChannels();
    }

    /**
     * Register broadcasting channels for conversations
     */
    private function registerBroadcastingChannels(): void
    {
        // This will be handled in routes/channels.php
        // But we can add any additional channel logic here if needed
    }

    /**
     * Get the events and handlers.
     *
     * @return array<string, array<int, string>>
     */
    public function listens(): array
    {
        return $this->listen;
    }
}