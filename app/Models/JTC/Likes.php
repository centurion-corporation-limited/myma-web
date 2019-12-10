<?php

namespace App\Models\JTC;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Likes extends Model
{
    protected $connection = "mysql_2";
    protected $table = "jtc_topic_likes";
    public $timestamps = [ "created_at" ];

    protected $fillable = [
        'topic_id', 'user_id'
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
        return $this->belongsTo(Detail::class);
    }
}
