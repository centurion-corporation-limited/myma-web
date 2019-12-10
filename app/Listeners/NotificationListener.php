<?php

namespace App\Listeners;

use App\Events\SendBrowserNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Models\Notification;
use App\Models\FcmToken;
use Mail;

class NotificationListener
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
    public function handle(SendBrowserNotification $event)
    {
        $message = $event->message;

        $user_id = '';
        $all_tokens = FcmToken::all();
        foreach($all_tokens as $token){
            sendBrowser($token->fcm_token, $message);
        }
        $filtered_tokens = FcmToken::groupBy('user_id')->get();
        foreach($filtered_tokens as $token){
          $user_id = $token->user_id;
          Notification::create(['type' => 'admin', 'title' => 'Transaction Notification', 'message' => $message, 'user_id' => $user_id, 'created_by' => $user_id]);
        }

    }
}
