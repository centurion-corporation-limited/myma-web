<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MaintenanceStatus
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $maintenance_id;
    public $from;
    public $to;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $id, $from, $to)
    {
        $this->user_id = $user_id;
        $this->maintenance_id = $id;
        $this->from = $from;
        $this->to = $to;
    }

}
