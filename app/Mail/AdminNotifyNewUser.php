<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminNotifyNewUser extends Mailable
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
            'name'              => $this->user->instagram_name? $this->user->instagram_name:$this->user->company_name,
            'email'             => $this->user->email,
            'user_id'			      => $this->user->id
        ];

        return $this->from(env('MAIL_FROM'))
                    ->subject('New Sign Up!')
                    ->view('emails.admin_notify_new_user')->with($data);
    }
}
