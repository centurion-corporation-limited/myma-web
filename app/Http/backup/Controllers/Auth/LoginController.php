<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use Activity;

use Illuminate\Http\Request;

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
    protected $redirectTo = '/admin/dashboard';

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

     // public function showLoginForm()
     // {
     //     return view('auth.login');
     // }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        /** @var User $user */
        $user = User::where($this->username(), $request->input($this->username()))->first();

        // if ($user && !$user->confirmed) {
        //     return redirect()->back()
        //         ->withInput($request->only($this->username(), 'remember'))
        //         ->withErrors([
        //             $this->username() => 'Please check your inbox to verify your account in order to proceed. Click <a class="verify" href="#">here</a> to resend verification link.',
        //         ]);
        // }


        if ($user && $user->hasRole('employee')) {
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => 'You are not an authorized staff member.',
                ]);
        }

        if ($user && $user->blocked) {
          return redirect()->back()
          ->withInput($request->only($this->username(), 'remember'))
          ->withErrors([
            $this->username() => 'Your account is blocked',
          ]);
        }

        if ($this->attemptLogin($request)) {
            Activity::log('User logged in - '.$user->username);
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);

    }

}
