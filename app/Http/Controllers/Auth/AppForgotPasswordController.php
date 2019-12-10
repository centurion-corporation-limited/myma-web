<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Event, Exception;
use App\User, Activity;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Events\SendOTP;

class AppForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
          //  'email' => 'required|max:255',
          //  'password' => 'required|min:6',
        ]);
    }

    public function checkMail(Request $request){
      try{
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          $msg = '';
          foreach($errors->messages() as $key => $error){
              $message[$key] = $error[0];
              $msg = $error[0];
          }

          return response()->json(['status' => 'error', 'error' => 'true', 'data' => $msg, 'message' => $msg], 401);
        }
        $searchValue = strtolower($request->email);
        $is_email = false;
        if(filter_var($searchValue, FILTER_VALIDATE_EMAIL)) {
            $is_email = true;
        }

        if($is_email){
          $users = User::all()->filter(function($record) use($searchValue) {
              $email = $record->email;
              try{
                  $email = Crypt::decrypt($email);
              }catch(DecryptException $e){
              }
              if($email == $searchValue) {
                  return $record;
              }
          });
          $user = [];
          if(count($users)){
              $user = $users->first();
          }
        }else{
          $user = User::whereHas('profile', function($q) use($searchValue){
            $q->where('phone', $searchValue);
          })->first();
        }

        // $user = User::where('email', $request->email)->first();
        if($user){
          if($user->blocked){
            return response()->json(['status' => 'error', 'error' => 'true', 'data' => 'Your account has been blocked. Please contact the administrator.', 'message' => 'Your account has been blocked. Please contact the administrator.']);
          }
          Activity::log('Forgot password request created');

          $otp = rand(1000, 9999);
          $user->update([
            'otp' => $otp
          ]);

          if($is_email){
            event(new SendOTP($user->id));
            return response()->json(['status' => 'success', 'error' => 'false', 'data' => 'An OTP has been sent to your Email ID.' , 'message' => 'An OTP has been sent to your Email ID.', 'otp' => $otp]);
          }else{
            $message = "Your One-Time Password (OTP) to reset your password is ".$otp.". This password will be expired after 10 minutes.";
            $phone = $user->profile->phone;
            if(strlen($phone) == 8){
              $phone = "+65".$phone;
            }
            sendSMS($phone, $message);
            return response()->json(['status' => 'success', 'error' => 'false', 'data' => 'An OTP has been sent to your mobile number.', 'message' => 'An OTP has been sent to your mobile number.', 'otp' => $otp]);
          }

          // Event::fire('user.send_otp', [$user->id]);
        }else{
          if($is_email)
            return response()->json(['status' => 'error', 'error' => 'true', 'data' => 'Email does not exists.', 'message' => 'Email does not exists.']);
          else
            return response()->json(['status' => 'error', 'error' => 'true', 'data' => 'mobile number does not exists.', 'message' => 'mobile number does not exists.']);

        }

      }catch(Exception $e){
        return response()->json(['status' => 'error', 'error' => 'true', 'data' => $e->getMessage(), 'message' => $e->getMessage()]);
      }
    }


    public function checkOTP(Request $request){
      try{
        $validator = Validator::make($request->all(), [
          'otp' => 'required',
          // 'email' => 'required|email|max:255',
          // 'password' => 'required|min:6',
        ]);
        if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
              $message[$key] = $error[0];
          }

          return response()->json(['status' => 'error', 'error' => 'true', 'message' => $message], 401);
        }

        $user = User::where('otp', $request->otp)->first();
        if($user){
          // $user->update([
            // 'otp' => ''
          // ]);

          return response()->json(['status' => 'success', 'error' => 'false', 'message' => 'Matched', 'id' => $user->id]);
        }else{
          return response()->json(['status' => 'error', 'error' => 'true', 'message' => 'OTP does not match.']);
        }

      }catch(Exception $e){
        return response()->json(['status' => 'error', 'error' => 'true', 'message' => $e->getMessage()]);
      }
    }

    public function updatePassword(Request $request){
      try{
        $validator = Validator::make($request->all(), [
            // 'otp' => 'required',
            'id' => 'required',
            'password' => 'required|min:8',
        ]);
        if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
              $message[$key] = $error[0];
          }

          return response()->json(['status' => 'error', 'error' => 'true', 'message' => $message], 401);
        }
        $user = User::where('id', $request->id)->first();
        // $user = User::where('email', $request->email)->first();
        if($user){

          $user->update([
            'password' => bcrypt($request->input('password')),
            'otp' => '',
            'password_retry' => 0
          ]);

          return response()->json(['status' => 'success', 'error' => 'false', 'message' => 'Password Updated']);
        }else{
          return response()->json(['status' => 'error', 'error' => 'true', 'message' => 'Invalid id.']);
        }

      }catch(Exception $e){
        return response()->json(['status' => 'error', 'error' => 'true', 'message' => $e->getMessage()]);
      }
    }
}
