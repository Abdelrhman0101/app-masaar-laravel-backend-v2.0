<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $status;
    public $lastSeen;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, string $status, $lastSeen = null)
    {
        $this->user = $user;
        $this->status = $status; // 'online', 'offline', 'away'
        $this->lastSeen = $lastSeen ?? now();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast to all conversations this user is part of
        $channels = [];
        
        // Get all conversations for this user
        $conversations = $this->user->conversations();
        
        foreach ($conversations as $conversation) {
            $channels[] = new PrivateChannel('chat.' . $conversation->id);
        }
        
        // Also broadcast to user's personal channel
        $channels[] = new PrivateChannel('user.' . $this->user->id);
        
        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'user.status.changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'user_type' => $this->user->user_type,
            ],
            'status' => $this->status,
            'last_seen' => $this->lastSeen->toISOString(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        // Always broadcast user status changes
        return true;
    }
}