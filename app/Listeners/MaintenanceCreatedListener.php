<?php

namespace App\Listeners;

use App\Events\MaintenanceCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Models\Maintenance;
use App\Models\Dormitory;
use Illuminate\Mail\Message;
use Mail;
use App\Mail\MaintenanceCreated;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM, Activity;

class MaintenanceCreatedListener
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
     * @param  MaintenanceCreated  $event
     * @return void
     */
    public function handle(MaintenanceCreatedEvent $event)
    {
        $maintenance_id = $event->maintenance_id;
        $dormitory_id = $event->dormitory_id;

        $module = Maintenance::find($maintenance_id);
        $dorm = Dormitory::find($dormitory_id);
        $user = @$dorm->manager;

        $users = User::whereHas('roles', function($q){
            $q->where('slug','dorm-maintainer');
        })->whereHas('profile', function($q) use($dormitory_id){
            $q->where('dormitory_id',$dormitory_id);
        })->get();

        if($module && $users->count()){
            foreach($users as $user){
                // if($user && $user->fcm_token){
                //     $token = $user->fcm_token;
                //     // $token = 'dty4Tudz9FY:APA91bHfS3HhlVvopaviuYH308m4plv90QU2wYBiBU9i2DddKLeXJf-sgk5wIl6vO7HRij2xTEsh-yPnBd5w7_0MY4fJ-ri5QHprdDyH4yWr2P_oV2ZH-zxoeD9gcYWTlNvioR2FlZWej6V5uBttddmw30UPSVxitA';
                //     $message = 'A new maintenance request posted #'.$module->id.' by '.$module->user->name;
                //     $this->sendSingle($token, $message, $maintenance_id);
                // }
                if($user && $user->email){
                    Mail::to($user->email)
                    // Mail::to('test34@yopmail.com')
                    // ->subject('New Maintenance request posted')
                    ->send(new MaintenanceCreated($user,$module));
                }
            }
        }
    }

    public function sendSingle($token, $msg, $id){

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $notificationBuilder = new PayloadNotificationBuilder('Dormitory Maintenance');
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
