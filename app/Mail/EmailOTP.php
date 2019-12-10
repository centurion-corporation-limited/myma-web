<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailOTP extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
       $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      $data = [
            'name'              => $this->user->name,
            'email'             => $this->user->email,
            'user_id'			      => $this->user->id,
            'otp'               => $this->user->otp
        ];

        return $this->from(env('MAIL_FROM'))->subject('MyMA App Password Reset OTP')->view('emails.send_otp')->with($data);
    }
}
