<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccountEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $pass)
    {
        $this->user = $user;
        $this->pass = $pass;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      $data = [
            'id'                => encrypt($this->user->id),
            'name'              => $this->user->name,
            'email'             => $this->user->email,
            'pass'              => $this->pass,
            'role'              => $this->user->hasRole('app-user')
        ];

        return $this->from(env('MAIL_FROM'))
                    ->subject('Welcome to Myma!')
                    ->view('emails.account_created')->with($data);
    }
}
