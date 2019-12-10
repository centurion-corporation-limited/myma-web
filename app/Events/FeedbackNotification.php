<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FeedbackNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $feedback_id;
    public $feedback;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($feedback_id, $feedback)
    {
        $this->feedback_id = $feedback_id;
        $this->feedback = $feedback;
    }

}
