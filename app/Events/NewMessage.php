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
        // البيانات التي سيتم إرسالها مع الحدث. يتم تحميل المرسل لسهولة استخدامه في الواجهة.
        return [
            'message' => $this->message->load('sender')
        ];
    }
}