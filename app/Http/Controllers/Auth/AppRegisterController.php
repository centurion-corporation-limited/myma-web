<?php

namespace App\Http\Controllers\Auth;

use App\Events\VerifyEmailEvent;
use App\User, JWTAuth;
use App\Helper\RandomStringGenerator;
use App\Models\UserProfile;
use App\Models\Dormitory;
use App\Models\Otp;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Auth, Activity;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Requests\AddFormRequest;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Carbon\Carbon;

class AppRegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    //use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if(isset($data['registered_using']) && $data['registered_using'] == 'ios'){
          $rules = [
              // 'name' => 'required|max:255',
              'email' => 'nullable|email|max:255',
              'phone' => 'required|unique:user_profile',
              'password' => ['required',
                 'min:8',
                 //'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/',
                 'confirmed'],
                //'required|min:8|confirmed',
              // 'wp_expiry' => 'required',
              'fin_no' => 'nullable|unique:user_profile',
              // 'profile_pic' => 'required',
              // 'wp_front' => 'required',
              // 'wp_back' => 'required',
              // 'gender' => 'required',
              // 'dob' => 'required',
              // 'country_id' => 'required',
              // 'street_address' => 'required',
              // 'block' => 'required',
              // 'sub_block' => 'required',
              // 'unit_no' => 'required',
              // 'floor_no' => 'required',
              // 'room_no' => 'required',
              // 'zip_code' => 'required',
              // 'dormitory_id' => 'required',
          ];
        }else{
            if(isset($data['residence_id'])){
            $rules = [
                'name' => 'required|max:255',
                'phone' => 'required|unique:user_profile',
                'password' => ['required',
                   'min:8',
                   //'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/',
                   'confirmed'],
                  //'required|min:8|confirmed',
                'fin_no' => 'required|unique:user_profile',
                'gender' => 'required',
                'dob' => 'required',
                'country_id' => 'required',
            ];
          }else{
            $rules = [
                'name' => 'required|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'required|unique:user_profile',
                'password' => ['required',
                   'min:8',
                   //'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/',
                   'confirmed'],
                  //'required|min:8|confirmed',
                // 'wp_expiry' => 'required',
                'fin_no' => 'required|unique:user_profile',
                'profile_pic' => 'required',
                'wp_front' => 'required',
                'wp_back' => 'required',
                'gender' => 'required',
                'dob' => 'required',
                'country_id' => 'required',
                // 'street_address' => 'required',
                // 'block' => 'required',
                // 'sub_block' => 'required',
                // 'unit_no' => 'required',
                // 'floor_no' => 'required',
                // 'room_no' => 'required',
                // 'zip_code' => 'required',
                // 'dormitory_id' => 'required',
            ];
          }
        }
        $messages = [
            'phone.required'    => 'The mobile number field is required.',
            'phone.unique'    => 'The mobile number has already been taken.',
        ];
        // if(isset($data['fin_no']) && $data['fin_no'] != ''){
        //     $rules['fin_no'] = 'required|unique:user_profile';
        //     $rules['wp_front'] = 'required';
        //     $rules['wp_back'] = 'required';
        // }
        return Validator::make($data, $rules, $messages);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function verifyFin($fin_no)
     {
         // $fin_no = 'G5098264K';
         try{
             $client = new Client(); //GuzzleHttp\Client
             $result = $client->get("http://residents.centurioncorp.com.sg/mymaapi/api/resident?json={'fin_no':'".$fin_no."','phone_no':'','gender':''}");
             $code = $result->getStatusCode(); // 200
             $reason = $result->getReasonPhrase(); // OK
             if($code == "200" && $reason == "OK"){
                 $body = $result->getBody();
                 $content = json_decode($body->getContents());

                 return $content;
             }else{
                 return false;
             }
         }catch(Exception $e){
             return false;
         }
     }

     public function sendEmailOtp(){
       $user = User::find(494);
       $val = event(new VerifyEmailEvent($user->id));
       dd($val);
    }
    
    protected function validator_otp(array $data)
    {
        $rules = [
            //'phone' => 'required|max:8|unique:user_profile',
        ];
        $messages = [
            'phone.unique'    => 'The mobile number has already been taken.',
        ];
        return Validator::make($data, $rules, $messages);
    }
    
    public function generateOTP(Request $request)
    {
        $validator = $this->validator_otp($request->all());
        \Log::debug('generate otp');
        \Log::debug($request->all());
        $flag = false;
        if($request->input('token')){
            $flag = true;
            $user = JWTAuth::toUser($request->input('token'));
        }

        if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
              $message = $error[0];
              break;
          }

          return response()->json(['status' => 'error', 'data' => $message, 'message' => $message], 200);
        }

        try{
          $otp = $data['otp'] = rand(1000, 9999);
          if($flag && $user->profile){
            $data['phone'] = $user->profile->phone;
            $user->update(['otp' => $otp]);
          }else{
            $data['phone'] = $request->input('phone');
            $profile = UserProfile::where('phone', $data['phone'])->first();
            if($profile){
                $user = User::where(['id' => $profile->user_id])->first();
                $user->update(['otp' => $otp]);    
            }
            
          }

          $phone = $data['phone'];
          if(strlen($phone) == 8){
              $phone = '+65'.$phone;
          }
          $message = "Your One-Time Password (OTP) to verify your mobile number is ".$otp.". This otp will be expired after 10 minutes.";
          sendSMS($phone, $message);
          \Log::debug($message);
          return response()->json(['status' => 'success', 'data' => '', 'message' => 'OTP_GENERATED', 'otp' => $otp]);

        }catch (Exception $e){
          \Log::debug($e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function verifyOTP(Request $request)
    {
        $validator = $this->validator_otp($request->all());
        $user = JWTAuth::toUser($request->input('token'));

        if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
              $message = $error[0];
              break;
          }
          return response()->json(['status' => 'error', 'data' => $message, 'message' => $message], 200);
        }

        try{
            $otp = $request->input('otp');
            if($user->otp != $otp){
              return response()->json(['status' => 'error', 'data' => '', 'message' => 'Otp does not match.'], 200);
            }
            $user->update(['otp' => '', 'number_verified' => 1]);

            return response()->json(['status' => 'success', 'data' => '', 'message' => 'Number verified successfully']);

        }catch (Exception $e){
          \Log::debug($e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }
    
    public function checkEmail(Request $request){
            $data['email'] = $request->email;
        
            $searchValue = strtolower($data['email']);
            if($searchValue != ""){
              $items = User::all()->filter(function($record) use($searchValue) {
                          $email = $record->email;
                          try{
                              $email = Crypt::decrypt($email);
                          }catch(DecryptException $e){

                          }
                          if(($email) == $searchValue) {
                              return $record;
                          }
              });
              if(count($items)){
                return response()->json(['status' => 'error', 'data' => 'The email has already been taken.', 'message' => 'The email has already been taken.'], 200);
              }
              return response()->json(['status' => 'error', 'data' => 'Not taken', 'message' => ''], 200);
            }
            return response()->json(['status' => 'error', 'data' => 'Missing email', 'message' => ''], 200);
    }
    public function registerCust(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
              $message = $error[0];
              break;
          }
          \Log::debug('validation error');
          \Log::debug(json_encode($message));
          return response()->json(['status' => 'error', 'data' => $message, 'message' => $message], 200);
        }
        try{

            $data = $request->all();
            if(@$data['otp'] != ''){
              $exist = Otp::where('phone', $data['phone'])->where('otp', @$data['otp'])->first();
              if(!$exist){
                $message = "Otp does not match";
                return response()->json(['status' => 'error', 'data' => $message, 'message' => $message], 200);
              }
            }
            //\Log::debug('registerCust');
            //\Log::debug($data);
            $searchValue = strtolower($data['email']);
            if($searchValue != ""){
              $items = User::all()->filter(function($record) use($searchValue) {
                          $email = $record->email;
                          try{
                              $email = Crypt::decrypt($email);
                          }catch(DecryptException $e){

                          }
                          if(($email) == $searchValue) {
                              return $record;
                          }
              });
              if(count($items)){
                return response()->json(['status' => 'error', 'data' => 'The email has already been taken.', 'message' => 'The email has already been taken.'], 200);
              }
            }

            $type = 'free';
            $dorm = '';
            $fin = '';
            if(isset($data['fin_no']) && $data['fin_no'] != ''){
                    $type = 'registered';
                    $fin = $this->verifyFin($data['fin_no']);
                    if($fin && $fin->verified){
                        $dorm = Dormitory::where('full_name', $fin->dormitory)->first();
                        $type = 'registered_verified';

                    }
            }
            
            $otp = $data['otp'] = rand(1000, 9999);
            $user = User::create([
                'name' => $data['name'],
                'email' => strtolower($data['email']),
                'password' => bcrypt($data['password']),
                'fcm_token' => @$data['fcm_token'],
                'type' => $type,
                'email_confirm_key' => str_random(),
                'number_verified' => 0,
                'otp' => $otp,
                'country_id' => @$data['country_id'],
                'residence_id' => @$data['residence_id']
            ]);

            if(isset($data['fin_no']) && $data['fin_no'] != ''){
                    \QrCode::format('png')->size(400)->generate($data['fin_no'], '../public/files/qrcodes/'.$user->id.'.png');
            }

            if($fin != '' && $type == 'registered'){
                Activity::log('Fin no not registered with centurioncorp', $user->id);
                $user->qr_code = 'files/qrcodes/'.$user->id.'.png';
            }
            if($type == 'registered_verified'){

                $user->qr_code = 'files/qrcodes/'.$user->id.'.png';
            }

            $user->save();

            if(@$data['profile_pic'] && @$data['profile_pic'] != ""){
              $photo = $data['profile_pic'];

              $folder = 'files/profile/';
              $photo_path = savePhoto($photo, $folder);
              $data['profile_pic'] = $photo_path;

            }
            if(isset($data['wp_front']) && $data['wp_front'] != ""){
              $photo = $data['wp_front'];

              $folder = 'files/permit/';
              $photo_path = savePhoto($photo, $folder);
              $data['wp_front'] = $photo_path;
            }
            if(isset($data['wp_back']) && $data['wp_back'] != ""){
              $photo = $data['wp_back'];

              $folder = 'files/permit/';
              $photo_path = savePhoto($photo, $folder);
              $data['wp_back'] = $photo_path;
            }

            $user_profile = [
                'phone' => @$data['phone'],
                'fin_no' => strToUpper(@$data['fin_no']),
                'profile_pic' => @$data['profile_pic'],
                'gender' => @$data['gender'],
                'dob' => date('Y-m-d',strtotime(@$data['dob'])),
                'block' => @$data['block'],
                'sub_block' => @$data['sub_block'],
                'floor_no' => @$data['floor_no'],
                'unit_no' => @$data['unit_no'],
                'room_no' => @$data['room_no'],
                'zip_code' => @$data['zip_code'],
                'street_address' => @$data['street_address'],
                'wp_front' => @$data['wp_front'],
                'wp_back' => @$data['wp_back'],
                'wp_expiry' => @$data['wp_expiry'] == ''?'':date('Y-m-d',strtotime(@$data['wp_expiry'])),
                'dormitory_id' => ($dorm != '')?$dorm->id:'',
            ];
            $user->profile()->create($user_profile);

            Activity::log('Account is created.', $user->id);
            /** Assign role*/
            $role = 'app-user';
            $user->assignRole($role);

            $generator = new RandomStringGenerator;
            $tokenLength = 32;

            $token = $generator->generate($tokenLength);
            $flag = true;
            while($flag){
                $exist = User::where('uid', $token)->first();
                if($exist){
                  $token = $generator->generate($tokenLength);
                }else{
                  $flag = false;
                }
            }
            $user->uid = $token;
            $user->save();

            //if($user->email != '')
              //event(new VerifyEmailEvent($user->id));

            // \Event::fire('user.created', [$user->id]);
            $phone = $data['phone'];
            if(strlen($phone) == 8){
              $phone = '+65'.$phone;
            }
            $message = "Your One-Time Password (OTP) to verify your mobile number is ".$otp.". This otp will be expired after 10 minutes.";
            sendSMS($phone, $message);

            $token = JWTAuth::fromUser($user);
            
            return response()->json(['status' => 'success', 'data' => 'Registration successfull.', 'message' => 'ACCOUNT_CREATED', 'token' => $token]);

        }catch (Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());

        }
    }

    public function registerCustCustom(Request $request)
    {
      $data = $request->all();

      //\Log::debug($data);
      if($_SERVER['REMOTE_ADDR'] == '122.173.187.228'){
        if(isset($data['dob']) && $data['dob'] != ''){
          $start = explode('/',$data['dob']);
          $data['dob'] = Carbon::create($start[2],$start[1],$start[0])->toDateString();
        }
        if(isset($data['wp_expiry']) && $data['wp_expiry'] != ''){
          $start = explode('/',$data['wp_expiry']);
          $data['wp_expiry'] = Carbon::create($start[2],$start[1],$start[0])->toDateString();
        }
      }
     // \Log::debug(json_encode($data));
      return true;
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
              $message = $error[0];
             break;
          }
          \Log::debug("validation issue ".json_encode($message));
          return response()->json(['status' => 'error', 'data' => $message, 'message' => $message], 200);
        }

        try{


            $searchValue = strtolower($data['email']);
            if($searchValue != ""){
              $items = User::all()->filter(function($record) use($searchValue) {
                          $email = $record->email;
                          try{
                              $email = Crypt::decrypt($email);
                          }catch(DecryptException $e){

                          }
                          if(($email) == $searchValue) {
                              return $record;
                          }
              });
              if(count($items)){
                  return response()->json(['status' => 'error', 'data' => ['email' => 'The email has already been taken.'], 'message' => 'VALIDATION_ERROR'], 200);
              }
            }

            $type = 'free';
            $dorm = '';
            $fin = '';
            if(isset($data['fin_no']) && $data['fin_no'] != ''){
                    $type = 'registered';
                    $fin = $this->verifyFin($data['fin_no']);
                    if($fin && $fin->verified){
                        $dorm = Dormitory::where('full_name', $fin->dormitory)->first();
                        $type = 'registered_verified';

                    }
            }
            $user = User::create([
                'name' => $data['name'],
                'email' => strtolower($data['email']),
                'password' => bcrypt($data['password']),
                'fcm_token' => @$data['fcm_token'],
                'type' => $type,
                'email_confirm_key' => str_random(),
            ]);

            if(isset($data['fin_no']) && $data['fin_no'] != ''){
                    \QrCode::format('png')->size(400)->generate($data['fin_no'], '../public/files/qrcodes/'.$user->id.'.png');
            }

            if($fin != '' && $type == 'registered'){
                Activity::log('Fin no not registered with centurioncorp', $user->id);
                $user->qr_code = 'files/qrcodes/'.$user->id.'.png';
            }
            if($type == 'registered_verified'){

                $user->qr_code = 'files/qrcodes/'.$user->id.'.png';
            }

            $user->save();

            if(@$data['profile_pic'] && @$data['profile_pic'] != ""){
              $photo = $data['profile_pic'];

              $folder = 'files/profile/';

              $photo_path = savePhotoDumy($photo, $folder);
              $data['profile_pic'] = $photo_path;

            }
            if(isset($data['wp_front']) && $data['wp_front'] != ""){
              $photo = $data['wp_front'];

              $folder = 'files/permit/';
              $photo_path = savePhotoDumy($photo, $folder);
              $data['wp_front'] = $photo_path;
            }
            if(isset($data['wp_back']) && $data['wp_back'] != ""){
              $photo = $data['wp_back'];

              $folder = 'files/permit/';
              $photo_path = savePhotoDumy($photo, $folder);
              $data['wp_back'] = $photo_path;
            }

            $user_profile = [
                'phone' => @$data['phone'],
                'fin_no' => strToUpper(@$data['fin_no']),
                'profile_pic' => @$data['profile_pic'],
                'gender' => @$data['gender'],
                'dob' => date('y-m-d',strtotime(@$data['dob'])),
                'block' => @$data['block'],
                'sub_block' => @$data['sub_block'],
                'floor_no' => @$data['floor_no'],
                'unit_no' => @$data['unit_no'],
                'room_no' => @$data['room_no'],
                'zip_code' => @$data['zip_code'],
                'street_address' => @$data['street_address'],
                'wp_front' => @$data['wp_front'],
                'wp_back' => @$data['wp_back'],
                'wp_expiry' => @$data['wp_expiry'] == ''?'':date('y-m-d',strtotime(@$data['wp_expiry'])),
                'dormitory_id' => ($dorm != '')?$dorm->id:'',
            ];
            $user->profile()->create($user_profile);

            Activity::log('Account is created.', $user->id);
            /** Assign role*/
            $role = 'app-user';
            $user->assignRole($role);

            $generator = new RandomStringGenerator;
            $tokenLength = 32;

            $token = $generator->generate($tokenLength);
            $flag = true;
            while($flag){
                $exist = User::where('uid', $token)->first();
                if($exist){
                  $token = $generator->generate($tokenLength);
                }else{
                  $flag = false;
                }
            }
            $user->uid = $token;
            $user->save();

            //if($user->email != '')
              //event(new VerifyEmailEvent($user->id));

            // \Event::fire('user.created', [$user->id]);

            return response()->json(['status' => 'success', 'data' => 'Registration successfull.', 'message' => 'ACCOUNT_CREATED']);

        }catch (Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());

        }
    }

    public function registerCustNew(Request $request)
    {
        $data = $request->except('profile_pic');
        //\Log::debug($data);
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
             $message[$key] = $error[0];
             break;
          }
          return redirect()->back()->withErrors($message)->withInput();
        }

        try{


            $searchValue = strtolower($data['email']);
            if($searchValue != ""){
              $items = User::all()->filter(function($record) use($searchValue) {
                          $email = $record->email;
                          try{
                              $email = Crypt::decrypt($email);
                          }catch(DecryptException $e){

                          }
                          if(($email) == $searchValue) {
                              return $record;
                          }
              });
              if(count($items)){
                  return redirect()->back()->withErrors(['email' => 'Email has already been taken.'])->withInput();
              }
            }

            $type = 'free';
            $dorm = '';
            $fin = '';
            if(isset($data['fin_no']) && $data['fin_no'] != ''){
                    $type = 'registered';
                    $fin = $this->verifyFin($data['fin_no']);
                    if($fin && $fin->verified){
                        $dorm = Dormitory::where('full_name', $fin->dormitory)->first();
                        $type = 'registered_verified';

                    }
            }
            $user = User::create([
                'name' => $data['name'],
                'email' => strtolower($data['email']),
                'password' => bcrypt($data['password']),
                'fcm_token' => @$data['fcm_token'],
                'type' => $type,
                'email_confirm_key' => str_random(),
                'number_verified' => 0,
                // 'otp' => $otp,
                'country_id' => @$data['country_id']
            ]);

            if(isset($data['fin_no']) && $data['fin_no'] != ''){
                    \QrCode::format('png')->size(400)->generate($data['fin_no'], '../public/files/qrcodes/'.$user->id.'.png');
            }

            if($fin != '' && $type == 'registered'){
                Activity::log('Fin no not registered with centurioncorp', $user->id);
                $user->qr_code = 'files/qrcodes/'.$user->id.'.png';
            }
            if($type == 'registered_verified'){

                $user->qr_code = 'files/qrcodes/'.$user->id.'.png';
            }

            $user->save();

            if($request->hasFile('profile_pic')){
              $file = $request->file('profile_pic');

              $folder = 'files/profile';

              if (!is_dir($folder)) {
                  mkdir($folder, 755, true);
              }
              $filename = $file->getClientOriginalName();

              $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
              $filename = str_replace("_", " ", $filename);
              $actual_name   = pathinfo($filename, PATHINFO_FILENAME);
              $original_name = $actual_name;
              $extension     = pathinfo($filename, PATHINFO_EXTENSION);

              $i = 1;
              while(file_exists($folder . '/' . $actual_name . "." . $extension))
              {
                  $actual_name = (string) $original_name . $i;
                  $filename    = $actual_name . "." . $extension;
                  $i++;
              }
              $full_file_name  = $folder . '/' . $filename;
              $file->move($folder, $filename);

              // $photo_path = uploadPhoto($photo, $folder);

              $data['profile_pic'] = $full_file_name;

            }

            if($request->hasFile('wp_front')){
              $file = $request->file('wp_front');

              $folder = 'files/permit';

              if (!is_dir($folder)) {
                  mkdir($folder, 755, true);
              }
              $filename = $file->getClientOriginalName();

              $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
              $filename = str_replace("_", " ", $filename);
              $actual_name   = pathinfo($filename, PATHINFO_FILENAME);
              $original_name = $actual_name;
              $extension     = pathinfo($filename, PATHINFO_EXTENSION);

              $i = 1;
              while(file_exists($folder . '/' . $actual_name . "." . $extension))
              {
                  $actual_name = (string) $original_name . $i;
                  $filename    = $actual_name . "." . $extension;
                  $i++;
              }
              $full_file_name  = $folder . '/' . $filename;
              $file->move($folder, $filename);

              // $photo_path = uploadPhoto($photo, $folder);

              $data['wp_front'] = $full_file_name;

            }

            if($request->hasFile('wp_back')){
              $file = $request->file('wp_back');

              $folder = 'files/permit';

              if (!is_dir($folder)) {
                  mkdir($folder, 755, true);
              }
              $filename = $file->getClientOriginalName();

              $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
              $filename = str_replace("_", " ", $filename);
              $actual_name   = pathinfo($filename, PATHINFO_FILENAME);
              $original_name = $actual_name;
              $extension     = pathinfo($filename, PATHINFO_EXTENSION);

              $i = 1;
              while(file_exists($folder . '/' . $actual_name . "." . $extension))
              {
                  $actual_name = (string) $original_name . $i;
                  $filename    = $actual_name . "." . $extension;
                  $i++;
              }
              $full_file_name  = $folder . '/' . $filename;
              $file->move($folder, $filename);

              // $photo_path = uploadPhoto($photo, $folder);

              $data['wp_back'] = $full_file_name;

            }

            // if(@$data['profile_pic'] && @$data['profile_pic'] != ""){
            //   $photo = $data['profile_pic'];
            //
            //   $folder = 'files/profile/';
            //
            //   $photo_path = savePhotoDumy($photo, $folder);
            //   $data['profile_pic'] = $photo_path;
            //
            // }
            // if(isset($data['wp_front']) && $data['wp_front'] != ""){
            //   $photo = $data['wp_front'];
            //
            //   $folder = 'files/permit/';
            //   $photo_path = savePhotoDumy($photo, $folder);
            //   $data['wp_front'] = $photo_path;
            // }
            // if(isset($data['wp_back']) && $data['wp_back'] != ""){
            //   $photo = $data['wp_back'];
            //
            //   $folder = 'files/permit/';
            //   $photo_path = savePhotoDumy($photo, $folder);
            //   $data['wp_back'] = $photo_path;
            // }


              if(isset($data['dob']) && $data['dob'] != ''){
                $data['dob'] = Carbon::createFromFormat('d/m/Y', $data['dob'])->toDateString();
                // $start = explode('/',$data['dob']);
                // if(@$start[2] && @$start[1] && @$start[0]){
                //     $data['dob'] = Carbon::create($start[2],$start[1],$start[0])->toDateString();
                // }
              }
              if(isset($data['wp_expiry']) && $data['wp_expiry'] != ''){
                $data['wp_expiry'] = Carbon::createFromFormat('d/m/Y', $data['wp_expiry'])->toDateString();
                // $start = explode('/',$data['wp_expiry']);
                // if(@$start[2] && @$start[1] && @$start[0]){
                //     $data['wp_expiry'] = Carbon::create($start[2],$start[1],$start[0])->toDateString();
                // }
              }

            $user_profile = [
                'phone' => @$data['phone'],
                'fin_no' => strToUpper(@$data['fin_no']),
                'profile_pic' => @$data['profile_pic'],
                'gender' => @$data['gender'],
                'dob' => @$data['dob'],
                'block' => @$data['block'],
                'sub_block' => @$data['sub_block'],
                'floor_no' => @$data['floor_no'],
                'unit_no' => @$data['unit_no'],
                'room_no' => @$data['room_no'],
                'zip_code' => @$data['zip_code'],
                'street_address' => @$data['street_address'],
                'wp_front' => @$data['wp_front'],
                'wp_back' => @$data['wp_back'],
                'wp_expiry' => @$data['wp_expiry'],
                'dormitory_id' => ($dorm != '')?$dorm->id:@$data['dormitory_id'],
            ];
            $user->profile()->create($user_profile);

            Activity::log('Account is created.', $user->id);
            /** Assign role*/
            $role = 'app-user';
            $user->assignRole($role);

            $generator = new RandomStringGenerator;
            $tokenLength = 32;

            $token = $generator->generate($tokenLength);
            $flag = true;
            while($flag){
                $exist = User::where('uid', $token)->first();
                if($exist){
                  $token = $generator->generate($tokenLength);
                }else{
                  $flag = false;
                }
            }
            $user->uid = $token;
            $user->save();

            //if($user->email != '')
              //event(new VerifyEmailEvent($user->id));

            // \Event::fire('user.created', [$user->id]);
            return redirect()->route('signup.form.success');

            //return response()->json(['status' => 'success', 'data' => 'Registration successfull.', 'message' => 'ACCOUNT_CREATED']);

        }catch (Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getConfirm($key)
    {
        /** @var User $user */
        $user = User::where('email_confirm_key', $key)->first();
        if ($user) {
            $user->update([
                'confirmed' => '1',
                'email_confirm_key' => '',
            ]);

            //\Event::fire('user.confirm-success', [$user->id]);
            if (Auth::login($user)) {
              return redirect()->to("profile/create")->withInput(['email' => $user->email])->with([
                'flash_level'   => 'success',
                'flash_message' => 'Your email has been verified. You can login now.'
              ]);
            }

            return redirect()->to("login")->withInput(['email' => $user->email])->with([
              'flash_level'   => 'success',
              'flash_message' => 'Your email has been verified. You can login now.'
            ]);
        }
        return redirect('/profile/create');
        // return view('frontend.confirm');
    }

    public function resendVerification($id)
    {
        /** @var User $user */
        $user = User::find($id);
        if ($user) {
            \Event::fire('user.reverification', [$user->id]);

            return view('auth.confirm_email')->withErrors([
                'email' => 'Please check your email to verify your account.'
            ]);
        }
        return redirect('/login');
    }
}
