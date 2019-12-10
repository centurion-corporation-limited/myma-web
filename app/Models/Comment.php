<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Comment extends Model
{
    protected $connection = "mysql_2";
    protected $table = "comments";
    public $timestamps = ["created_at"];

    protected $fillable = [
        'forum_id', 'comment', 'user_id', 'flag'
    ];

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public function user()
    {
        return $this->belongsTo(User::class);//->withTrashed();
    }

    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }

}
