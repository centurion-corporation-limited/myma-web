<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Forum;
use App\Models\Topic;


class Likes extends Model
{
    protected $connection = "mysql_2";
    protected $table = "likes";
    public $timestamps = [ "created_at" ];

    protected $fillable = [
        'ref_id', 'user_id', 'type'
    ];

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function forum()
    {
        return $this->belongsTo(Forum::class, 'ref_id')->where('type', 'forum');
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'ref_id')->where('type', 'topic');
    }

    public function is_liked($id, $type)
    {
        $result = $this->where('type', '=', $type)->where('ref_id', $id);
        return $result;
    }
}
