<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Feedback;

class FeedbackReplyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($reply)
    {
       $this->reply = $reply;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $feedback = Feedback::findOrFail($this->reply->feedback_id);
        $data = [
            'feedback'              => $this->reply->feedback,
            'feedback_replied'      => 'WLC Team',
            'feedback_creator'      => $feedback->name
        ];

        return $this->from(env('MAIL_FROM'))->view('emails.feedback_reply')->subject('Feedback Reply')->with($data);
    }
}
