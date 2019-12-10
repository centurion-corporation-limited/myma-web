<?php

namespace App\Models\JTC;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Comment extends Model
{
    protected $connection = "mysql_2";
    protected $table = "jtc_topic_comments";
    public $timestamps = ["created_at"];

    protected $fillable = [
        'topic_id', 'comment', 'user_id'
    ];

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public function user()
    {
        return $this->belongsTo(User::class);//->withTrashed();
    }

    public function topic()
    {
        return $this->belongsTo(Detail::class, 'topic_id', 'id');
    }

}
