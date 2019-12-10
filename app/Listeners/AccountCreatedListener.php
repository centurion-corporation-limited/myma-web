<?php

namespace App\Listeners;

use App\Events\AccountCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use Mail;
use App\Mail\AccountEmail;

class AccountCreatedListener
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
     * @param  AccountCreated  $event
     * @return void
     */
    public function handle(AccountCreated $event)
    {
        $user_id = $event->user_id;
        $pass = $event->pass;

        $user = User::findOrFail($user_id);
        if($user)
            Mail::to($user->email)->send(new AccountEmail($user, $pass));
    }
}
