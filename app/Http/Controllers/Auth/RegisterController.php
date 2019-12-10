<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Models\UserProfile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Auth;

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
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'fin_no' => 'required',
            'wp_expiry' => 'required',
            'gender' => 'required',
            'phone_no' => 'required',
            'dob' => 'required',
            'street_address' => 'required',
            'block' => 'required',
            'sub_block' => 'required',
            'unit_no' => 'required',
            'floor_no' => 'required',
            'room_no' => 'required',
            'zip_code' => 'required',
            'dormitory_id' => 'required',
        ];

        // if($role == 'influencer')
        //   $rules['instagram_name'] = 'required|max:255';
        // else
        //   $rules['company_name'] = 'required|max:255';

        return Validator::make($data, $rules);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerCust(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $data = $request->all();

        $user = User::create([
          'name' => $data['name'],
          'email' => $data['email'],
          'password' => bcrypt($data['password']),
          // 'email_confirm_key' => str_random(),
        ]);

        $user_profile = [
            'phone' => $data['phone'],
            'gender' => $data['gender'],
            'dob' => $data['dob'],
            'block' => $data['block'],
            'sub_block' => $data['sub_block'],
            'floor_no' => $data['floor_no'],
            'unit_no' => $data['unit_no'],
            'room_no' => $data['room_no'],
            'zip_code' => $data['zip_code'],
            'street_address' => $data['street_address'],
            'wp_front' => $data['wp_front'],
            'wp_back' => $data['wp_back'],
            'wp_expiry' => $data['wp_expiry'],
            'dormitory_id' => $data['dormitory_id'],
        ];
        $user->profile()->create($user_profile);

      /** Assign role*/
      // $data['type'] = $role;
      // $user->assignRole(filter_var($data['type'],FILTER_SANITIZE_STRING));

        // \Event::fire('user.created', [$user->id]);

        return response()->json(['status' => 'success', 'data' => 'Registration successfull.', 'message' => 'ACCOUNT_CREATED']);

        // return view('auth.confirm_email')->withErrors([
        //     'email' => 'Please check your email to verify your account.'
        // ]);
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

            // dd($user);
            //\Event::fire('user.confirm-success', [$user->id]);
            // return redirect()->route("confirm.success");
            // ->with([
            //   'flash_level'   => 'success',
            //   'flash_message' => 'Your email has been verified. You can login now.'
            // ]);
            return view('frontend.success');
        }
        return abort('404');
    }

    public function getSuccess()
    {
        return view('frontend.success');
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
