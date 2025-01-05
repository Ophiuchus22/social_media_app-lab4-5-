<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $notification;

    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    public function broadcastAs()
    {
        return 'NewNotification';
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->notification->user_id);
    }

    public function broadcastWith()
    {
        return [
            'notification' => $this->notification
        ];
    }
} 