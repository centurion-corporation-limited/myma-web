<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Forum extends Model
{
    protected $connection = "mysql_2";
    protected $table = "forum";
    public $timestamps = ["created_at"];

    protected $fillable = [
        'title', 'share', 'topic_id', 'content', 'user_id','report'
    ];

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'forum_id');
    }

    public function likes()
    {
        return $this->hasMany(Likes::class, 'ref_id')->where('type', '=', 'forum');;
    }

}
