<?php

namespace App\Listeners;

use App\Events\SendOTP;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use Mail, Activity;
use App\Mail\EmailOTP;

class SendOtpListener
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
     * @param  SendOTP  $event
     * @return void
     */
    public function handle(SendOTP $event)
    {

        $user = User::findOrFail($event->user_id);
        if($user){
            //$user->email
          $log = Mail::to($user->email)
            // ->subject('Otp for password reset')
            ->send(new EmailOTP($user));

            Activity::log('Sent otp email for password reset.', $user->id);
        }
    }
}
