<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyEmail extends Mailable
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
            'email_confirm_key' => $this->user->email_confirm_key,
            'user_id'			=> $this->user->id,
            'type'			    => $this->user->type
        ];

        return $this->from(env('MAIL_FROM'))
                    ->subject('Welcome to Myma!')
                    ->view('emails.confirm')->with($data);
    }
}
