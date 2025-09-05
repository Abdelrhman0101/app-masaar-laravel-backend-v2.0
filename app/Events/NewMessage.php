<?php

namespace App\Events;

use App\Models\Message; // <-- هذا السطر مهم جداً
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The message instance.
     *
     * @var \App\Models\Message
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Message $message
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // بث الحدث على قناة خاصة بالمحادثة لضمان الأمان والخصوصية
        return new PrivateChannel('chat.' . $this->message->conversation_id);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        // اسم الحدث الذي سيتم الاستماع إليه في الواجهة الأمامية (Flutter)
        return 'new.message';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        // تحميل البيانات المطلوبة مع الرسالة
        $this->message->load(['sender:id,name,user_type', 'conversation']);
        
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'content' => $this->message->content,
                'message_type' => $this->message->message_type,
                'is_system_message' => $this->message->is_system_message,
                'read_at' => $this->message->read_at,
                'created_at' => $this->message->created_at,
                'updated_at' => $this->message->updated_at,
                'sender' => $this->message->sender ? [
                    'id' => $this->message->sender->id,
                    'name' => $this->message->sender->name,
                    'user_type' => $this->message->sender->user_type
                ] : null,
                'conversation' => [
                    'id' => $this->message->conversation->id,
                    'type' => $this->message->conversation->type,
                    'status' => $this->message->conversation->status
                ]
            ],
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * تحديد الشروط التي يجب أن تتحقق لإرسال البث
     */
    public function broadcastWhen()
    {
        // إرسال البث دائماً للرسائل العادية
        // للرسائل النظام، إرسال البث فقط إذا كان مطلوباً
        return !$this->message->is_system_message || 
               ($this->message->is_system_message && ($this->message->metadata['broadcast'] ?? true));
    }

    /**
     * تحديد المستخدمين الذين يمكنهم الوصول للقناة
     */
    public function broadcastToPresenceChannel()
    {
        // يمكن استخدامها لاحقاً لتحديد من يمكنه الوصول للقناة
        return $this->message->conversation->participants()->pluck('id')->toArray();
    }
}