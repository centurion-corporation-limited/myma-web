<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use JWTAuth, DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Location;
use App\Models\UserProfile;
use Activity, App\User;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\RedeemUser;
use GuzzleHttp\Client;

class AppLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required',
            'password' => 'required',
        ]);
    }


	public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('username', 'password');
        $validator = $this->validator($credentials);

        if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
              $message = $error[0];
              break;
          }

          return response()->json(['status' => 'error', 'data' => $message, 'message' => $message], 401);
        }
        if($credentials['username'] == 'test2@yopmail.com'){
            
        }else{
         //   return response()->json(['status' => 'error', 'data' => "Waiting for launch", 'message' => 'Waiting for launch'], 401);    
        }
        $redeemTouch = RedeemUser::where('mobile',$credentials['username'])->where('type','TOUCH')->count(); 
        $redeemTouchCredited = RedeemUser::where('mobile',$credentials['username'])->where('type','TOUCH')->where('status','credit_successful')->count(); 
        
        
        $credentials['email'] = strtolower($credentials['username']);
        unset($credentials['username']);
        try {
            $profile = UserProfile::where('phone', $credentials['email'])->first();
            if($profile){
              $credentials['id'] = $profile->user_id;
              unset($credentials['email']);
            }else{
              $items = User::all()->filter(function($record) use($credentials) {
                  $searchValue = $credentials['email'];
                  $email = $record->email;
                  try{
                      $email = Crypt::decrypt($email);
                  }catch(DecryptException $e){
                  }
                  if($email == $searchValue) {
                      return $record;
                  }
              });
              if(count($items)){
                  $credentials['id'] = $items->first()->id;
                  unset($credentials['email']);
                  // return response()->json(['status' => 'error', 'data' => $credentials, 'message' => 'INVALID_CREDENTIALS'], 401);
              }
            }

            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                // Activity::log('Logged in successfully');

                $user = User::where(['id' => @$credentials['id']])->first();
                if($user){
                    $retry = $user['password_retry']+1;
                    $d = ['password_retry' => $retry];
                    // if($retry >= 3){
                    //     $d['blocked'] = '1';
                    // }
                    $user->update($d);
                    // if($retry >= 3){
                    //     return response()->json(['status' => 'error', 'data' => NULL, 'message' => 'INVALID_CREDENTIALS'], 401);
                    // }
                }
                return response()->json(['status' => 'error', 'data' => NULL, 'message' => 'INVALID CREDENTIALS'], 401);
            }

            $user = JWTAuth::toUser($token);
            //user not confirmed
            if($user->blocked){
                Activity::log('login failed because account is blocked.');

              return response()->json(['status' => 'error', 'data' => '', 'message' => 'Your account has been blocked. Please contact the administrator.'], 401);
            }
            if($user->password_retry > 2){
              Activity::log('Account blocked because user entered wrong password more than 3 times.');

              return response()->json(['status' => 'error', 'data' => '', 'message' => 'You need to reset your password to login.'], 401);
            }
            $fcm_token = $request->input('fcm_token');
            if($fcm_token != ''){
                $user->update(['fcm_token' => $fcm_token]);
            }
            if($token != ''){
                $user->update(['token' => $token]);
            }

            // //if not employee
            // if(!$user->hasRole('employee')){
            //   return response()->json(['status' => 'error', 'data' => 'Invalid Credentials.', 'message' => 'INVALID_CREDENTIALS'], 401);
            // }

            LoginHistory::create([
                'user_id' => $user->id,
                'type'    => 'login',
                'logged_time' => \Carbon\Carbon::now()->toDateTimeString()
            ]);
            
            $phone = @$user->profile->phone;
            if(strlen($phone) == 8 && $user->number_verified == 0){
                $otp = $data['otp'] = rand(1000, 9999);
                $user->update(['otp' => $otp]);
                  $phone = '+65'.$phone;
                  $message = "Your One-Time Password (OTP) to verify your mobile number is ".$otp.". This otp will be expired after 10 minutes.";
                  sendSMS($phone, $message);
            }
              
          
            Activity::log('Logged in successfully');
            $type = $user->type;
            if($user->hasRole('restaurant-owner-catering')){
              $type = 'catering';
            }elseif($user->hasRole('restaurant-owner-single')){
              $type = 'single';
            }
            return response()->json([
              'status'  => 'success',
              'data'    => [
                'token' => $token,
                'type' => $type,
                'flexm' => $user->flexm_account,
                'number_verified' => $user->number_verified == ""?0:$user->number_verified,
                'isTouchRedeemed' => $redeemTouchCredited  > 0 ? true:false,
                'isTouchRequested' => $redeemTouch > 0 ? true:false,
                'isTouchActive' => true
              ],
              'message' => 'LOGIN_SUCCESS'

            ]);

        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['status' => 'error', 'data' => NULL, 'message' => 'COULD NOT CREATE TOKEN'], $e->getStatusCode());
        }

        // all good so return the token
    }

    protected function social_validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|max:255',
            // 'password' => 'required|min:6',
        ]);
    }

    public function socialLogin(Request $request)
    {

        // grab credentials from the request
        $credentials = $request->only('email');
        $validator = $this->social_validator($credentials);
        $data = $request->only('email', 'phone', 'profile_pic', 'dob', 'gender', 'type', 'access_token', 'name');
        if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
              $message[$key] = $error[0];
          }

          return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }

        try {

            $items = User::all()->filter(function($record) use($credentials) {
                $searchValue = strtolower($credentials['email']);
                $email = $record->email;
                try{
                    $email = Crypt::decrypt($email);
                }catch(DecryptException $e){
                }
                if($email == $searchValue) {
                    return $record;
                }
            });
            if(count($items)){
                $exist = $items->first();
            }else{
                $exist = User::where('email', $credentials['email'])->first();
            }
            if(!$exist){
                // $exist = User::create([
                //     'name'      => (isset($data['name']) && $data['name'] != '')?$data['name']:$data['email'],
                //     'email'     => $data['email'],
                // ]);

                // \QrCode::format('png')->size(400)->generate($data['email'], '../public/files/qrcodes/'.$exist->id.'.png');

                // $exist->qr_code = 'files/qrcodes/'.$exist->id.'.png';
                // $exist->save();

                // if(isset($data['profile_pic']) && $data['profile_pic'] != ""){
                //   $photo = $data['profile_pic'];

                //   $folder = 'files/profile/';
                //   $photo_path = savePhoto($photo, $folder);
                //   $data['profile_pic'] = $photo_path;
                // }

                // $user_profile = [
                //     'phone' => $data['phone'],
                //     'profile_pic' => $data['profile_pic'],
                //     'gender' => $data['gender'],
                //     'dob' => $data['dob'],
                // ];
                // $exist->profile()->create($user_profile);
                return response()->json(['status' => 'error', 'data' => 'Signup first to use social-login to sign in', 'message' => 'CREATE_ACCOUNT'], 401);
            }
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::fromUser($exist)) {
                // Activity::log('Logged in successfully');

                return response()->json(['status' => 'error', 'data' => NULL, 'message' => 'INVALID_CREDENTIALS'], 401);
            }

            $user = JWTAuth::toUser($token);

            //user not confirmed
            if($user->blocked){
                Activity::log('login failed because account is blocked.');

              return response()->json(['status' => 'error', 'data' => 'You account is blocked.', 'message' => 'ACCOUNT_BLOCKED'], 401);
            }
            if($user->password_retry > 2){
              Activity::log('Account blocked because user entered wrong password more than 3 times.');

              return response()->json(['status' => 'error', 'data' => '', 'message' => 'You need to reset your password to login.'], 401);
            }
            // //if not employee
            // if(!$user->hasRole('employee')){
            //   return response()->json(['status' => 'error', 'data' => 'Invalid Credentials.', 'message' => 'INVALID_CREDENTIALS'], 401);
            // }

            // $schedules =  DB::table('schedule as s')
            //  ->join('locations as l', 's.location_id', '=', 'l.id')
            //  ->join('users as u', 's.employee_id', '=', 'u.id')
            //  ->join('users as us', 's.created_by', '=', 'us.id')
            //  ->select('u.id as user_id', 'u.name as employee_name', 'us.name as created_by','l.latitude','l.longitude',
            //   's.arrival_time', 's.end_time')
            //  ->where('s.employee_id',$user->id);
            //  if($time = $request->input('arrival_time')){
            //    $schedules->whereDate('s.arrival_time', $time);
            //  }elseif($time = $request->input('after_time')){
            //    $schedules->whereDate('s.arrival_time', '>', $time);
            //  }
             //->orderBy('s.created_at', 'desc')
            //  $schedules = $schedules->get();
            // $locations =  Location::select('id','name','radius','longitude','latitude')->get();

            LoginHistory::create([
                'user_id' => $user->id,
                'type'    => 'login',
                'logged_time' => \Carbon\Carbon::now()->toDateTimeString()
            ]);
            Activity::log('Logged in successfully using social login');
            
            $type = $user->type;
            if($user->hasRole('restaurant-owner-catering')){
              $type = 'catering';
            }elseif($user->hasRole('restaurant-owner-single')){
              $type = 'single';
            }
            
            return response()->json([
              'status'  => 'success',
              'data'    => [
                'token' => $token,
                'type' => $type,
                'flexm' => $user->flexm_account,
                'number_verified' => $user->number_verified == ""?0:$user->number_verified,
                // 'employee_name' => $user->name,
                // 'employee_grade' => $user->grade,
                // 'employee_nric' => $user->nric,
                // 'geo-fencing' => $locations
              ],
              'message' => 'LOGIN_SUCCESS'

            ]);

        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['status' => 'error', 'data' => NULL, 'message' => 'COULD_NOT_CREATE_TOKEN'], $e->getStatusCode());
        }

        // all good so return the token
    }

    public function logout(Request $request){
      try{

        $token = $request->input('token');
        $user = JWTAuth::toUser($token);
        \Log::debug('Logout response');
        \Log::debug($user);
        //$return = JWTAuth::invalidate($token);
        LoginHistory::create([
            'user_id' => @$user->id,
            'type'    => 'logout',
            'logged_time' => \Carbon\Carbon::now()->toDateTimeString()
        ]);

        return response()->json(['status' => 'success', 'data' => NULL, 'message' => 'LOGOUT_SUCCESS']);

      }catch(JWTException $e){
        return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message' => 'TOKEN_EXPIRED'], $e->getStatusCode());

      }
    }
    


    /*public function redeem(Request $request)
    {  
        try{
            $token = $request->input('token');
            $type = $request->input('type');

            $request_user = JWTAuth::toUser($token);
            $request_name=$request_user->name;
            $request_userID=$request_user->id;

            $user_profile = UserProfile::where('user_id', $request_userID)->first();  
            $request_mobile=$user_profile->phone;

            if($type == 'TOUCH'){
                $touchUserCount = RedeemUser::where('type',$type)->count();
                if($touchUserCount > 50000) {
                    return response()->json(['status' => 'error', 'data' => "", 'message' => 'Redemption has fully redeem. Once again Thank You for your hard work'], 200);
                }
            }
            $redeemUser = RedeemUser::where('name',$request_name)->where('mobile',$request_mobile)->where('user_id', $request_userID)->where('type',$type)->count(); 
            if($redeemUser > 0){
                return response()->json(['status' => 'error', 'data' => "", 'message' => 'User already redeemed for type '.$type], 200);
            } else{  
                $user = RedeemUser::create([              
                        'user_id'       => $request_userID, 
                        'ih_user_id'    => $request->input('ih_user_id'),
                        'name'          => $request_name,
                        'mobile'        => $request_mobile,
                        'type'          => $type,
                        'fin_no'          => $user_profile->fin_no,
                        'click_redeem'   => 'yes',
                        //'click_date' => \Carbon\Carbon::now()->toDateTimeString(),
                        'status'          => 'redeem_successful',
                    ]);
                $user->save();    
                return response()->json(['status' => 'success', 'data' => $user, 'message' => 'User redeemed successfully for type '.$type], 200);
            }
        }catch(JWTException $e){          
            return response()->json(['status' => 'error', 'data' => NULL, 'message' => 'Token Missing or Incorrect'], $e->getStatusCode());
        }
    }*/


    public function redeem(Request $request)
    {  
        try{
            $token = $request->input('token');
            $type = $request->input('type');

            $request_user = JWTAuth::toUser($token);
            $request_name=$request_user->name;
            $request_userID=$request_user->id;

            $user_profile = UserProfile::where('user_id', $request_userID)->first();  
            $request_mobile=$user_profile->phone;

            if($type == 'TOUCH'){
                $touchUserCount = RedeemUser::where('type',$type)->count();
                if($touchUserCount > 50000) {
                    return response()->json(['status' => 'error', 'data' => "", 'message' => 'Redemption has fully redeem. Once again Thank You for your hard work'], 200);
                }
            }

            $redeemFinExists = RedeemUser::where('fin_no',$user_profile->fin_no)->count(); 
            $redeemUser = RedeemUser::where('ih_user_id',$request->input('ih_user_id'))->count(); 

            if($redeemUser > 0){
                return response()->json(['status' => 'error', 'data' => "", 'message' => 'User already redeemed for type '.$type], 200);
            } elseif($redeemFinExists > 0){
                return response()->json(['status' => 'error', 'data' => "", 'message' => 'FIN already redeemed for type '.$type], 200);
            }else{ 
                $client = new Client();
                $data = [];
                $result = $client->get('https://wallet.flexm.sg/api/user/user_id/'.$request->input('ih_user_id'));
                $code = $result->getStatusCode(); // 200
                $reason = $result->getReasonPhrase(); // OK
                if($code == "200" && $reason == "OK"){
                    $body = $result->getBody();
                    $content = json_decode($body->getContents());
                    $eWallet_mobile = $content->mobile;
                }else{
                    return response()->json(['status' => 'error', 'data' => "", 'message' => 'Mobile not available in FlexM eWallet'], 200);
                }    

                $user = RedeemUser::create([              
                        'user_id'       => $request_userID, 
                        'ih_user_id'    => $request->input('ih_user_id'),
                        'name'          => $request_name,
                        'mobile'        => $eWallet_mobile,
                        'myma_mobile'        => $request_mobile,
                        'type'          => $type,
                        'fin_no'          => $user_profile->fin_no,
                        'click_redeem'   => 'yes',
                        'status'          => 'redeem_successful',
                    ]);
                $user->save();    
                return response()->json(['status' => 'success', 'data' => $user, 'message' => 'User redeemed successfully for type '.$type], 200);
            }
        }catch(JWTException $e){          
            return response()->json(['status' => 'error', 'data' => NULL, 'message' => 'Token Missing or Incorrect'], $e->getStatusCode());
        }
    }

}
