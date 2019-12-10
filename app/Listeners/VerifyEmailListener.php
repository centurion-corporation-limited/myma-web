<?php

namespace App\Listeners;

use App\Events\VerifyEmailEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use Mail;
use App\Mail\VerifyEmail;

class VerifyEmailListener
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
     * @param  VerifyEmail  $event
     * @return void
     */
    public function handle(VerifyEmailEvent $event)
    {
        $user_id = $event->user_id;

        $user = User::findOrFail($user_id);
        if($user)
            Mail::to($user->email)->send(new VerifyEmail($user));
    }
}
