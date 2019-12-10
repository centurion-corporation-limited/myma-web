<?php
namespace App\Listeners;

use App\User;
use App\Events\FeedbackReplyEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\FeedbackReply;
use App\Models\Feedback;

use Event;
use Illuminate\Mail\Message;
use Mail;
use App\Mail\FeedbackReplyEmail;


class FeedbackListener
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
     * @event user.created
     *
     * Send email confirm
     *
     * @param $user_id
     */
    public function handle(FeedbackReplyEvent $event)
    {
        $reply_id = $event->reply_id;
        $item = FeedbackReply::findOrFail($reply_id);
        if($item){
            Mail::to($item->question->email)->send(new FeedbackReplyEmail($item));
        }
    }
}
