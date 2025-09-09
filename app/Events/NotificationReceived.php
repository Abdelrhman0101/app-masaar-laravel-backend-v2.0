<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        // سنبث الحدث على قناة خاصة بالمستخدم الذي سيستقبل الإشعار
        return new PrivateChannel('App.Models.User.' . $this->notification->user_id);
    }

    public function broadcastAs()
    {
        // هذا هو اسم الحدث الذي ستستمع إليه الواجهة الأمامية
        return 'new-notification';
    }
}