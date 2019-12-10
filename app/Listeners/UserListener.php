<?php
namespace App\Listeners;

use App\User;
use App\Models\Maintenance;
use App\Models\Dormitory;
use App\Models\Notification;
use Event;
use Illuminate\Mail\Message;
use Mail;
use App\Mail\VerifyEmail;
use App\Mail\AccountEmail;
use App\Mail\EmailOTP;
use App\Mail\ApproveEmail;
use App\Mail\ContactedEmail;
use App\Mail\EmailConfirmed;
use App\Mail\AppConfirmed;
use App\Mail\MaintenanceCreated;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM, Activity;

class UserListener
{
    /**
     * @event user.created
     *
     * Send email confirm
     *
     * @param $user_id
     */
    public function onCreated($user_id)
    {
        $user = User::findOrFail($user_id);
        if($user){
            $mail = Mail::to($user->email)->send(new VerifyEmail($user));
            // $message = 'Successfully created account as '.$user->type.' user';
            // Notification::create(['type' => 'general', 'title' => 'Account Created', 'message' => $message, 'user_id' => $user->id, 'created_by' => $user->id]);
            // if($user->fcm_token){
            //
            // }
        }

    }

    public function onAccountCreated($user_id, $pass)
    {
        $user = User::findOrFail($user_id);
        if($user)
            Mail::to($user->email)->send(new AccountEmail($user, $pass));

    }

    public function onResendVerification($user_id)
    {
        $user = User::findOrFail($user_id);
        Mail::to($user->email)->send(new VerifyEmail($user));

    }

    public function onApprove($user_id)
    {
        $user = User::findOrFail($user_id);
        Mail::to($user->email)->send(new ApproveEmail($user));
    }

    public function onPasswordChanged($user_id)
    {
        $user = User::findOrFail($user_id);
        $data['data'] = [
            'name'        => $user->name,
			      'user_id'			=> $user_id
        ];

        Mail::send('emails.password-changed', $data, function (Message $m) use ($user) {
            $m->to($user->email);
            $m->subject('Password changed');
        });
    }

    public function onConfirmSuccess($user_id)
    {
        $user = User::findOrFail($user_id);

        Mail::to($user->email)
            //->subject('Confirm email success');
            ->queue(new EmailConfirmed($user));
    }

    public function onVerified($user_id)
    {
        $user = User::findOrFail($user_id);
        $data['data'] = [
            'name'        => $user->name,
			      'user_id'			=> $user_id
        ];

        Mail::queue('emails.user-verified', $data, function (Message $m) use ($user) {
            $m->to($user->email);
            $m->subject('Your account is verified');
        });
    }

    public function onSend($user_id)
    {
        $user = User::findOrFail($user_id);
        if($user){
            $log = Mail::to($user->email)
            // ->subject('Otp for password reset')
            ->send(new EmailOTP($user));
        }

    }

    public function onMaintenanceCreate($maintenance_id, $dormitory_id)
    {
        $module = Maintenance::find($maintenance_id);
        $dorm = Dormitory::find($dormitory_id);
        $user = $dorm->manager;
        // $users = User::whereHas('role', function($q){
        //     $q->where('slug','dorm-maintainer');
        // })->whereHas('profile', function($q) use($dormitory_id){
        //     $q->where('dormitory_id',$dormitory_id);
        // })->get();

        // if($module && $users->count()){
            // foreach($users as $user){
                if($user && $user->fcm_token){
                    $token = $user->fcm_token;
                    // $token = 'f7NaRU40dRc:APA91bGHwCj6qdkskWgVgapNcnD2otEzbGCamRdKR-n0odri80cgfPKBZje7bgDejz4WCl6xI16gOxb4QanV0PeA_E5fBmVMDlZ9J2NwN2JTw8mk2brR4PXyKUobH3xBMxcJ6f4EvNF1txIxSjyvUrehmPObZjUJ5w';
                    $message = 'A new maintenance request posted #'.$module->id.' by '.$user->name;
                    $this->sendSingle($token, $message, $maintenance_id);
                }
                if($user && $user->fcm_token){
                    Mail::to($user->email)
                    // Mail::to('test81@yopmail.com')
                    //->subject('Confirm email success');
                    ->queue(new MaintenanceCreated($user,$module));
                }
            // }
        // }

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
