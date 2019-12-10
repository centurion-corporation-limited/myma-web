<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MaintenanceCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $dormitory_id;
    public $maintenance_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($maintenance_id, $dormitory_id)
    {
        $this->maintenance_id = $maintenance_id;
        $this->dormitory_id = $dormitory_id;
    }

}
