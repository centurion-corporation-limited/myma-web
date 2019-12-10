<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use JWTAuth, DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Location;
use Activity, App\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class MerchantLoginController extends Controller
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
            'username' => 'required|max:255',
            'password' => 'required|min:6',
        ]);
    }

	public function authenticate(Request $request)
    {
        // grab credentials from the request
        $user_type = $request->input('type');
        $credentials = $request->only('username', 'password');
        $validator = $this->validator($credentials);

        if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
              $message[$key] = $error[0];
          }

          return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 200);
        }

        $credentials['email'] = strtolower($credentials['username']);
        unset($credentials['username']);
        try {
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
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                // Activity::log('Logged in successfully');

                return response()->json(['status' => 'error', 'data' => NULL, 'message' => 'INVALID_CREDENTIALS'], 200);
            }

            $user = JWTAuth::toUser($token);
            $fcm_token = $request->input('fcm_token');
            if($fcm_token != ''){
                $user->update(['fcm_token' => $fcm_token]);
            }
            //user not confirmed
            if($user->blocked){
                Activity::log('login failed because account is blocked.');

              return response()->json(['status' => 'error', 'data' => 'You account is blocked.', 'message' => 'ACCOUNT_BLOCKED'], 200);
            }
            // //if not employee
            // if(!$user->hasRole('employee')){
            //   return response()->json(['status' => 'error', 'data' => 'Invalid Credentials.', 'message' => 'INVALID_CREDENTIALS'], 200);
            // }

            Activity::log('Logged in successfully');
            $roles = $user->getRoles();
            if(count($roles)){
                foreach($roles as $role){
                    $roles = $role;
                    break;
                }
            }else{
                $roles = "restaurant-owner-package";
            }
            return response()->json([
              'status'  => 'success',
              'data'    => [
                'token' => $token,
                'type' => $user->type,
                'role' => $roles
              ],
              'message' => 'LOGIN_SUCCESS'

            ]);

        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['status' => 'error', 'data' => NULL, 'message' => 'COULD_NOT_CREATE_TOKEN'], $e->getStatusCode());
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

          return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 200);
        }

        try {
            $exist = User::where('email', $credentials['email'])->first();
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
                return response()->json(['status' => 'error', 'data' => 'Signup first to use social-login to sign in', 'message' => 'CREATE_ACCOUNT'], 200);
            }
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::fromUser($exist)) {
                // Activity::log('Logged in successfully');

                return response()->json(['status' => 'error', 'data' => NULL, 'message' => 'INVALID_CREDENTIALS'], 200);
            }

            $user = JWTAuth::toUser($token);

            //user not confirmed
            if($user->blocked){
                Activity::log('login failed because account is blocked.');

              return response()->json(['status' => 'error', 'data' => 'You account is blocked.', 'message' => 'ACCOUNT_BLOCKED'], 200);
            }
            // //if not employee
            // if(!$user->hasRole('employee')){
            //   return response()->json(['status' => 'error', 'data' => 'Invalid Credentials.', 'message' => 'INVALID_CREDENTIALS'], 200);
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
            Activity::log('Logged in successfully using social login');
            return response()->json([
              'status'  => 'success',
              'data'    => [
                'token' => $token,
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

    public function logout(){
      try{
        $return = JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['status' => 'success', 'data' => NULL, 'message' => 'LOGOUT_SUCCESS']);

      }catch(JWTException $e){
        return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message' => 'TOKEN_REQUIRED'], $e->getStatusCode());

      }
    }

}
