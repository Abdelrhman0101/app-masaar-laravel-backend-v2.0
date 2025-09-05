<?php

namespace App\Listeners;

use App\Events\NewMessage;
use App\Notifications\NewMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendNewMessageNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewMessage $event): void
    {
        try {
            // Check if notifications are enabled
            if (!config('conversation.notifications.enabled')) {
                return;
            }

            $message = $event->message;
            $sender = $event->sender;
            $conversation = $message->conversation;

            // Get all participants except the sender
            $recipients = $conversation->participants()
                ->where('user_id', '!=', $sender->id)
                ->get();

            if ($recipients->isEmpty()) {
                return;
            }

            // Send notification to each recipient
            foreach ($recipients as $recipient) {
                try {
                    $recipient->notify(new NewMessageNotification($message, $sender));
                } catch (\Exception $e) {
                    Log::error('Failed to send notification to user', [
                        'user_id' => $recipient->id,
                        'message_id' => $message->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('New message notifications sent', [
                'message_id' => $message->id,
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'recipients_count' => $recipients->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle NewMessage event for notifications', [
                'message_id' => $event->message->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(NewMessage $event, \Throwable $exception): void
    {
        Log::error('SendNewMessageNotification job failed', [
            'message_id' => $event->message->id ?? null,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}