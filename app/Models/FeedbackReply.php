<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class FeedbackReply extends Model
{
    protected $connection = "mysql_2";
    protected $table = "feedback_reply";
    public $timestamps = true;

    protected $fillable = [
        'feedback_id', 'user_id', 'feedback'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Feedback::class, 'feedback_id');
    }

}
