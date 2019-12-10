<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FeedbackReply
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $feedback_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($feedback_id)
    {
        $this->feedback_id = $feedback_id;
    }

}
