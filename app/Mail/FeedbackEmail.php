<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FeedbackEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $feedback;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'name'         => $this->feedback->name,
            'email'        => $this->feedback->email,
            'phone'        => $this->feedback->phone,
            'content'      => $this->feedback->content,
            'rating'       => $this->feedback->rating,
            'category'     => @$this->feedback->category->name,
        ];

        return $this->from(env('MAIL_FROM'))
                    ->subject('Feedback from Myma app user!')
                    ->view('emails.feedback')->with($data);
    }
}
