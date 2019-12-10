<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User, Activity, Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use Illuminate\Http\Request;
use Carbon\Carbon;

class LoginController extends Controller
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
    protected $redirectTo = '/admin';

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
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function showLoginForm(Request $request)
     {
         $url = \URL::previous();
         // \Log::debug($url);
         // if(strpos($url, 'login') == true && $_SERVER['REMOTE_ADDR'] == '122.176.82.110'){
         //
         //     dd(count($request->session()->all()['errors']));
         // }

         if(strpos($url, 'check') == false && !count(@$request->session()->all()['errors'])){
             return redirect()->to('https://myhype.space/check.html');
         }
         if(strpos($url, 'merchant') !== false){
             return view('frontend.merchant.login');
         }

         return view('auth.login');
     }

     // public function username()
     // {
     //    return 'id';
     // }

    public function login(Request $request)
    {
        $url = \URL::previous();
        // if(strpos($url, 'public/login') !== false){
        //      $type = '1';//admin
        // }
        // if(strpos($url, 'merchant') !== false){
        //      $type = '2';//merchant
        // }
        // if(strpos($url, 'driver') !== false){
        //      $type = '3';//driver
        // }
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        /** @var User $user */
        $searchValue = $request->input($this->username());
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
        if(count($users)){
            $user = $users->first();
        }else{
            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);

        }

        // $user = User::where($this->username(), $request->input($this->username()))->first();

        // if ($user && !$user->confirmed) {
        //     return redirect()->back()
        //         ->withInput($request->only($this->username(), 'remember'))
        //         ->withErrors([
        //             $this->username() => 'Please check your inbox to verify your account in order to proceed. Click <a class="verify" href="'.route('resend.verification', $user->id).'">here</a> to resend verification link.',
        //         ]);
        // }

        if ($user && (/*$user->hasRole('app-user') ||*/ $user->hasRole('driver') )) {
            //|| $user->hasRole('restaurant-owner-catering') || $user->hasRole('restaurant-owner-single')
            Activity::log('Tried to login to admin panel', $user->id);

            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => 'These credentials do not match our records.',
                ]);
        }
        if ($user && $user->hasRole('employee')) {
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => 'You are not an authorized staff member.',
                ]);
        }

        if ($user && $user->blocked) {

            Activity::log('Blocked user tried to login', $user->id);

          return redirect()->back()
          ->withInput($request->only($this->username(), 'remember'))
          ->withErrors([
            $this->username() => 'Your account is blocked',
          ]);
        }

        // if ($user && $user->hasRole('restaurant-owner')) {
        //     $this->redirectTo = '/merchant/dashboard';
        // }

        if ($user && $user->hasRole('driver')) {
            $this->redirectTo = '/driver/dashboard';
        }
        $credentials = $request->only('password');
        $credentials['id'] = $user->id;

        $current = Carbon::now();
        $dd['last_logged'] = $current->toDateTimeString();
        $last = Carbon::parse($user->last_logged);
        $diff = $current->diffInMinutes($last);

        if($user->id == 1 ||$diff > config('session.lifetime') || $user->last_logged == ''){
            if (Auth::attempt($credentials)) {//$this->attemptLogin($request)
                // if($user && !($user->hasRole('admin') || $user->hasRole('sub-admin'))){
                    Activity::log('Successfully logged into admin panel', $user->id);
                // }

                $user->update($dd);
                return $this->sendLoginResponse($request);
            }
        }else{
            return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => 'Multiple logins with same account are not allowed.',
            ]);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);

    }

    // public function logout(Request $request)
    // {
    //     $user = Auth::user();
    //     if($user)
    //         $user->update(['last_logged' => NULL]);
    //
    //     $this->guard()->logout();
    //
    //     $request->session()->invalidate();
    //
    //     return redirect('/');
    // }

}
