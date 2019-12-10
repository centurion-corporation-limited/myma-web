<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Forum extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "forum";
    protected $dates = ['reported_at'];
    public $timestamps = true;

    protected $fillable = [
        'title', 'share', 'topic_id', 'content', 'user_id','report', 'bad_word'
    ];

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

    public function latestComment(){
        return $this->hasMany(Comment::class, 'forum_id')->orderBy('id','desc')->limit(1);
    }
}
