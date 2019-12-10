<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\User, Activity;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class ForgotPasswordController extends Controller
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

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);
        $searchValue = strtolower($request->input('email'));
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
            $user = array();
        }
        // $user = User::where('email', $request->only('email'))->first();

        if($user && $user->hasRole('app-user')){
            return redirect()->back()
                ->withErrors([
                    'email' => "We can't find a user with that e-mail address.",
                ]);
        }
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        if($user)
            $data['id'] = $user->id;
        else
            $data = $request->only('email');

        $response = $this->broker()->sendResetLink(
            $data//$request->only('email')
        );

        Activity::log('User requested for password reset - '.@$data['id']);

        return $response == \Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Validate the email for the given request.
     *
     * @param \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);//, 'question' => 'required', 'answer' => 'required']);
    }
}
