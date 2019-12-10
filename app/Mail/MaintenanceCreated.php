<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MaintenanceCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $maintenance;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $maintenance)
    {
        $this->user = $user;
        $this->maintenance = $maintenance;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'name'              => @$this->user->name,
            'fin_no'            => @$this->user->profile->fin_no,
            'case_id'           => $this->maintenance->id,
            'location'          => $this->maintenance->location,
            'comments'          => $this->maintenance->comments,
            'dorm'              => @$this->maintenance->dormitory->name,
            'photo_1'           => @$this->maintenance->photo_1,
            'photo_2'           => @$this->maintenance->photo_2,
            'photo_3'           => @$this->maintenance->photo_3,
            'photo_4'           => @$this->maintenance->photo_4,
            'photo_5'           => @$this->maintenance->photo_5,
        ];

        return $this->from(env('MAIL_FROM'))
                    ->subject('New Maintenance Request')
                    ->view('emails.maintenance')->with($data);
    }
}
