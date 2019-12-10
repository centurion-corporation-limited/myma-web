<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotifyAdmin
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $wlc_amt;
    public $wlc_count;
    public $remit_amt;
    public $remit_count;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($wlc_amt, $wlc_count, $remit_amt, $remit_count)
    {
      $this->wlc_amt = $wlc_amt;
      $this->wlc_count = $wlc_count;
      $this->remit_amt = $remit_amt;
      $this->remit_count = $remit_count;
    }

}
