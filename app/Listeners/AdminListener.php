<?php

namespace App\Listeners;

use App\Events\NotifyAdmin;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use Illuminate\Mail\Message;
use Mail;
use App\Mail\NotifyError;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM, Activity;

class AdminListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NotifyError  $event
     * @return void
     */
    public function handle(NotifyAdmin $event)
    {
        $data['wlc_amt'] = $event->wlc_amt;
        $data['wlc_count'] = $event->wlc_count;
        $data['remit_amt'] = $event->remit_amt;
        $data['remit_count'] = $event->remit_count;

        Mail::to('test34@yopmail.com')
            ->send(new NotifyError($data));
    }
}
