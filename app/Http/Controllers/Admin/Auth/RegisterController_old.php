<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class RegisterController extends Controller
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

    use RegistersUsers;

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

    public function showInfluencerForm()
    {
        return view('auth.register');
    }

    public function showManagerForm()
    {
        return view('auth.register_manager');
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
            // /'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ];

        if($role == 'influencer')
          $rules['instagram_name'] = 'required|max:255';
        else
          $rules['company_name'] = 'required|max:255';

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data, $role)
    {
        if($role == 'influencer'){
          $user = User::create([
            'instagram_name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
          ]);

        }else{
          $user = User::create([
            'company_name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
          ]);

        }

        /** Assign role*/
        $data['type'] = $role;
        $user->assignRole(filter_var($data['type'],FILTER_SANITIZE_STRING));

        //\Event::fire('user.created', [$user->id]);

        return $user;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerCustom($role, Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $this->create($request->all(), $role);

        return redirect()->back()->withErrors([
            'email' => 'Please check email to confirm account'
        ]);
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

            return redirect()->to("login")->withInput(['email' => $user->email]);
        }

        return view('frontend.confirm');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        $field = filter_var($request->input('name'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';


        $credentials = $this->getCredentials($request);
        if($field == 'email'){
          $credentials[$field] = $credentials["name"];
          unset($credentials["name"]);
        }

        /** @var User $user */
        $user = User::where($this->username, $request->input($this->username))->first();
        if ($user && !$user->confirmed) {
            return redirect()->back()
                ->withInput($request->only($this->loginUsername(), 'remember'))
                ->withErrors([
                    $this->loginUsername() => 'Your account is not confirmed',
                ]);
        }

        if ($user && $user->blocked) {
            return redirect()->back()
                ->withInput($request->only($this->loginUsername(), 'remember'))
                ->withErrors([
                    $this->loginUsername() => 'Your account is blocked',
                ]);
        }

        if (Auth::guard($this->getGuard())->attempt($credentials, $request->has('remember'))) {
            session_start();
            $_SESSION['user'] = str_random(5) . base64_encode(Auth::user()->name);
            return $this->handleUserWasAuthenticated($request, $throttles);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles && ! $lockedOut) {
            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    // overrided method for showing model on error
    /**
     * Get the failed login response instance.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage(),
            ]);
    }
    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->secondsRemainingOnLockout($request);

        return redirect()->back()
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getLockoutErrorMessage($seconds),
            ]);
    }

    /**
     * Create the response for when a request fails validation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $errors
     * @return \Illuminate\Http\Response
     */
    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        if (($request->ajax() && ! $request->pjax()) || $request->wantsJson()) {
            return new JsonResponse($errors, 422);
        }

        return redirect()->to($this->getRedirectUrl())
                        ->withInput($request->input())
                        ->withErrors($errors, $this->errorBag());
    }
}
