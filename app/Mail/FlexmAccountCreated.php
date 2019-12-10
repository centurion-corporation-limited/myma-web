<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FlexmAccountCreated extends Mailable
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
          'id' => $this->user->email,
          'pass' => ''
        ];

        if($this->user->flexm_direct){

        }else{
          if($this->user->flexm_cron){
            $data['pass'] = 'Password@123';
          }
        }
        return $this->from(env('MAIL_FROM'))
                    ->subject('MyMA Wallet registered successful !')
                    ->view('emails.flexm_account_created')->with($data);
    }
}
