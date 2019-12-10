<?php

namespace App\Listeners;

use App\Events\SendFeedbackEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Models\Feedback;
use Mail;
use App\Mail\FeedbackEmail;

class SendFeedbackListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendFeedbackEvent  $event
     * @return void
     */
    public function handle(SendFeedbackEvent $event)
    {
        $feedback_id = $event->feedback_id;

        $feedback = Feedback::findOrFail($feedback_id);
        if($feedback){
          //update email to mom email
          Mail::to('check@yopmail.com')->send(new FeedbackEmail($feedback));
        }

    }
}
