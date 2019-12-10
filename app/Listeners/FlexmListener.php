<?php

namespace App\Listeners;

use App\Events\NotifyFlexmRegistration;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use Illuminate\Mail\Message;
use Mail;
use App\Mail\FlexmAccountCreated;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM, Activity;
use App\Models\Notification;

class FlexmListener
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
     * @param  NotifyFlexmRegistration  $event
     * @return void
     */
    public function handle(NotifyFlexmRegistration $event)
    {
        $user_id = $event->user_id;

        $user = User::findOrFail($user_id);

        // if($user && $user->fcm_token){
        //     $token = $user->fcm_token;
        //     $message = 'You have successfully registered your flexm account.';
        //     Notification::create(['type' => 'general', 'title' => 'Flexm registation successfull', 'message' => $message, 'user_id' => $user->id, 'created_by' => $user->id]);
        //     // $this->sendSingle($token, $message);
        // }
        if($user && $user->email){
            Mail::to($user->email)
            // Mail::to('test34@yopmail.com')
                    //->subject('Confirm email success');
            ->send(new FlexmAccountCreated($user));
        }
    }

    public function sendSingle($token, $msg, $id = ''){

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $notificationBuilder = new PayloadNotificationBuilder('Flexm Registration');
        $notificationBuilder->setBody($msg)->setSound('default')->setClickAction('FCM_PLUGIN_ACTIVITY');
        $dataBuilder = new PayloadDataBuilder();

        $dataBuilder->addData([
            'type' => 'wallet'
        ]);

        if($id != ''){
            $dataBuilder->addData([
                'id' => $id
            ]);
        }

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
        //return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();
        //return Array (key : oldToken, value : new token - you must change the token in your database )
        $downstreamResponse->tokensToModify();
        //return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();
        return response()->json(['status' => 'success', 'data' => 'notification sent', 'message' => ''], 200);

    }
}
