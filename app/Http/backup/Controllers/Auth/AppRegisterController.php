<?php

namespace App\Http\Controllers\Auth;

use App\Events\VerifyEmailEvent;
use App\User;
use App\Helper\RandomStringGenerator;
use App\Models\UserProfile;
use App\Models\Dormitory;
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
        $rules = [
            'name' => 'required|max:255',
            'email' => 'nullable|email|max:255',
            'password' => ['required',
               'min:8',
               //'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/',
               'confirmed'],
              //'required|min:8|confirmed',
            // 'wp_expiry' => 'required',
            'gender' => 'required',
            'phone' => 'required|unique:user_profile',
            'dob' => 'required',
            // 'street_address' => 'required',
            // 'block' => 'required',
            // 'sub_block' => 'required',
            // 'unit_no' => 'required',
            // 'floor_no' => 'required',
            // 'room_no' => 'required',
            // 'zip_code' => 'required',
            // 'dormitory_id' => 'required',
        ];
        if(isset($data['fin_no']) && $data['fin_no'] != ''){
            $rules['fin_no'] = 'required|unique:user_profile';
            $rules['wp_front'] = 'required';
            $rules['wp_back'] = 'required';
        }
        return Validator::make($data, $rules);
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
          \Log::debug("validation issue ".json_encode($message));
          return response()->json(['status' => 'error', 'data' => $message, 'message' => $message], 200);
        }
        try{

            $data = $request->all();
            \Log::debug('registerCust');

            \Log::debug($data);
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
                  return response()->json(['status' => 'error', 'data' => ['email' => 'The email has already been taken.'], 'message' => 'VALIDATION_ERROR'], 401);
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

    public function registerCustCustom(Request $request)
    {
      $data = $request->all();

      \Log::debug($data);
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
      \Log::debug(json_encode($data));
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
                  return response()->json(['status' => 'error', 'data' => ['email' => 'The email has already been taken.'], 'message' => 'VALIDATION_ERROR'], 401);
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
        \Log::debug($data);
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
                $start = explode('/',$data['dob']);
                $data['dob'] = Carbon::create($start[2],$start[1],$start[0])->toDateString();
              }
              if(isset($data['wp_expiry']) && $data['wp_expiry'] != ''){
                $start = explode('/',$data['wp_expiry']);
                $data['wp_expiry'] = Carbon::create($start[2],$start[1],$start[0])->toDateString();
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
