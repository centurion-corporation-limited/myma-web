<?php

namespace App\Listeners;

use App\Events\FeedbackNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use Illuminate\Mail\Message;
use Mail;
use App\User;
use App\Models\FeedbackReply;
use App\Models\Feedback;

class FeedbackNotificationListener
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
     * @param  FeedbackNotification  $event
     * @return void
     */
    public function handle(FeedbackNotification $event)
    {
        $feedback_id = $event->feedback_id;
        $reply = $event->feedback;

        $feedback = Feedback::findOrFail($feedback_id);

        $searchValue = $feedback['email'];
        $users = User::all()->filter(function($record) use($searchValue) {
            $email = $record->email;
            try{
                $email = Crypt::decrypt($email);
            }catch(DecryptException $e){
            }
            if(strToLower($email) == strToLower($searchValue)) {
                return $record;
            }
        });
        if($users->count()){
            $user = $users->first();
            if($user && $user->fcm_token){
                $token = $user->fcm_token;
                $message = $reply;
                // $token = 'c2csAcHNzmE:APA91bFcgvNoH-w0ML8JclXqUQbuwIAajYMLto2dk2jQVva7psVhQzn8rYABg8vQKEUi9PhbLlmz4iGl-Udl6OX9PBZo03M9LpddUq9W-PWxxIh7-HFvVVpIECCT0huHaJD4FtAvFDrG';
                $this->sendSingle($token, $message);
            }
        }
        // $user = User::where('email',$feedback['email'])->first();
    }

    public function sendSingle($token, $msg, $id = null){

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $notificationBuilder = new PayloadNotificationBuilder('Feedback Reply');
        $notificationBuilder->setBody($msg)->setSound('default')->setClickAction('FCM_PLUGIN_ACTIVITY');
        $dataBuilder = new PayloadDataBuilder();

        $dataBuilder->addData([
            'type' => 'maintenance'
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
