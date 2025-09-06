<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
// NOTE: FCM channel classes are referenced dynamically to avoid IDE errors when the package isn't installed
// use NotificationChannels\Fcm\FcmChannel;
// use NotificationChannels\Fcm\FcmMessage;
// use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $sender;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message, User $sender)
    {
        $this->message = $message;
        $this->sender = $sender;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Add FCM channel only if package exists and user has device tokens
        $fcmChannel = 'NotificationChannels\\Fcm\\FcmChannel';
        if (class_exists($fcmChannel) && method_exists($notifiable, 'deviceTokens') && $notifiable->deviceTokens()->exists()) {
            $channels[] = $fcmChannel;
        }
        
        // Add email for important conversations (admin-user type)
        if ($this->message->conversation->type === 'admin_user' && $notifiable->email) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $senderName = $this->sender->name;
        $conversationType = $this->getConversationTypeLabel();
        
        return (new MailMessage)
            ->subject("رسالة جديدة من {$senderName}")
            ->greeting("مرحباً {$notifiable->name}")
            ->line("لديك رسالة جديدة في {$conversationType}")
            ->line("من: {$senderName}")
            ->line("الرسالة: " . $this->getMessagePreview())
            ->action('عرض المحادثة', $this->getConversationUrl())
            ->line('شكراً لاستخدامك تطبيقنا!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'user_type' => $this->sender->user_type,
            ],
            'message_preview' => $this->getMessagePreview(),
            'conversation_type' => $this->message->conversation->type,
            'conversation_title' => $this->message->conversation->title,
            'created_at' => $this->message->created_at->toISOString(),
        ];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable)
    {
        $senderName = $this->sender->name;
        $conversationType = $this->getConversationTypeLabel();
        
        $fcmMessageClass = 'NotificationChannels\\Fcm\\FcmMessage';
        $fcmNotificationClass = 'NotificationChannels\\Fcm\\Resources\\Notification';
    
        // Safety: if package isn't present, this method shouldn't be called (channel won't be added)
        if (!class_exists($fcmMessageClass) || !class_exists($fcmNotificationClass)) {
            return new \stdClass();
        }
        
        $notification = new $fcmNotificationClass(
            title: "رسالة جديدة من {$senderName}",
            body: $this->getMessagePreview(),
            image: null
        );
    
        $message = new $fcmMessageClass(notification: $notification);
        
        // Chain configurations dynamically
        $message->data([
            'type' => 'new_message',
            'message_id' => (string) $this->message->id,
            'conversation_id' => (string) $this->message->conversation_id,
            'sender_id' => (string) $this->sender->id,
            'sender_name' => $senderName,
            'conversation_type' => $this->message->conversation->type,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ]);
    
        $message->android(
            config: [
                'priority' => 'high',
                'notification' => [
                    'channel_id' => 'messages',
                    'sound' => 'default',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
            ]
        );
    
        $message->apns(
            config: [
                'aps' => [
                    'sound' => 'default',
                    'badge' => $this->getUnreadCount($notifiable),
                    'category' => 'MESSAGE_CATEGORY',
                ],
            ]
        );
    
        return $message;
    }

    /**
     * Get message preview for notification
     */
    private function getMessagePreview(): string
    {
        $content = $this->message->content;
        
        if ($this->message->type === 'image') {
            return '📷 صورة';
        } elseif ($this->message->type === 'file') {
            return '📎 ملف';
        } elseif ($this->message->type === 'system') {
            return '🔔 ' . $content;
        }
        
        // Truncate text messages
        return strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content;
    }

    /**
     * Get conversation type label in Arabic
     */
    private function getConversationTypeLabel(): string
    {
        return match ($this->message->conversation->type) {
            'admin_user' => 'محادثة الدعم الفني',
            'user_provider' => 'محادثة مع مقدم الخدمة',
            'user_user' => 'محادثة شخصية',
            default => 'محادثة',
        };
    }

    /**
     * Get conversation URL
     */
    private function getConversationUrl(): string
    {
        $baseUrl = config('app.frontend_url', config('app.url'));
        return "{$baseUrl}/conversations/{$this->message->conversation_id}";
    }

    /**
     * Get unread messages count for badge
     */
    private function getUnreadCount(object $notifiable): int
    {
        return $notifiable->unreadMessages()->count();
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        // Don't send notification to the sender
        if ($notifiable->id === $this->sender->id) {
            return false;
        }
        
        // Don't send notification for system messages unless it's important
        if ($this->message->type === 'system' && !$this->isImportantSystemMessage()) {
            return false;
        }
        
        // Check user notification preferences
        if ($channel === 'mail' && !$this->shouldSendEmail($notifiable)) {
            return false;
        }
        
        $fcmChannel = 'NotificationChannels\\Fcm\\FcmChannel';
        if ($channel === $fcmChannel && !$this->shouldSendPush($notifiable)) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if system message is important
     */
    private function isImportantSystemMessage(): bool
    {
        $importantKeywords = ['مغلقة', 'مفتوحة', 'مؤرشفة', 'انضم', 'غادر'];
        
        foreach ($importantKeywords as $keyword) {
            if (str_contains($this->message->content, $keyword)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if should send email notification
     */
    private function shouldSendEmail(object $notifiable): bool
    {
        // Check user preferences (assuming there's a preferences column or relation)
        return $notifiable->email_notifications ?? true;
    }

    /**
     * Check if should send push notification
     */
    private function shouldSendPush(object $notifiable): bool
    {
        // Check user preferences
        return $notifiable->push_notifications ?? true;
    }
}