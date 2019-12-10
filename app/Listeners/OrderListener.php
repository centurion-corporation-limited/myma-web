<?php
namespace App\Listeners;

use App\User;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\OrderItem;
use Event;
use Illuminate\Mail\Message;
use Mail;
use App\Mail\VerifyEmail;
use App\Mail\EmailOTP;
use App\Mail\ApproveEmail;
use App\Mail\ContactedEmail;
use App\Mail\AdminNotifyNewUser;
use App\Mail\AdminNotifyNewBrandManager;
use App\Mail\EmailConfirmed;
use App\Mail\AppConfirmed;
use App\Mail\CampaignUpdateEmail;
use App\Mail\PaymentConfirmed;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class UserListener
{
    /**
     * @event user.created
     *
     * Send email confirm
     *
     * @param $user_id
     */
    public function onCreated($order_id)
    {
        $user = Order::findOrFail($order_id);

    }

    public function onMaintenanceUpdate($user_id, $maintenance_id, $from, $to)
    {

        $user = User::findOrFail($user_id);
        $module = Maintenance::findOrFail($maintenance_id);

        if($user->fcm_token){
            $token = $user->fcm_token;
            $message = 'Status of the maintenance #'.$module->id.' has be updated from '.$from.' to '.$to;
            // $token = 'c2csAcHNzmE:APA91bFcgvNoH-w0ML8JclXqUQbuwIAajYMLto2dk2jQVva7psVhQzn8rYABg8vQKEUi9PhbLlmz4iGl-Udl6OX9PBZo03M9LpddUq9W-PWxxIh7-HFvVVpIECCT0huHaJD4FtAvFDrG';
            $this->sendSingle($token, $message, $maintenance_id);
        }

    }

    public function sendSingle($token, $msg, $id){

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $notificationBuilder = new PayloadNotificationBuilder('New order received');
        $notificationBuilder->setBody($msg)->setSound('default')->setClickAction('FCM_PLUGIN_ACTIVITY');
        $dataBuilder = new PayloadDataBuilder();

        $dataBuilder->addData([
            'type' => 'order_received'
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
