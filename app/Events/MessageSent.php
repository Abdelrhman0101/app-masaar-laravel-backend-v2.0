<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
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
* We will broadcast on a private channel for the specific conversation.
* This ensures only users involved in this conversation will receive the event.
* The channel name will be something like 'chat.5' for conversation ID 5.
*
* @return \Illuminate\Broadcasting\Channel|array
*/
public function broadcastOn()
{
return new PrivateChannel('chat.' . $this->message->conversation_id);
}

/**
* The event's broadcast name.
*
* By default, Laravel uses the class name. We can define a custom one.
* Let's name it 'new-message'.
*
* @return string
*/
public function broadcastAs()
{
return 'new-message';
}

/**
* Get the data to broadcast.
*
* This determines what data is sent with the event.
* We will load the sender's info along with the message.
*
* @return array
*/
public function broadcastWith()
{
// نُحمّل بيانات المرسل مع الرسالة قبل إرسالها
$this->message->load('sender:id,name,profile_image');

return ['message' => $this->message];
}
}