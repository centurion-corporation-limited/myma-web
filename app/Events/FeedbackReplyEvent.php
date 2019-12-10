<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FeedbackReplyEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reply_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($reply_id)
    {
        $this->reply_id = $reply_id;
    }

}
