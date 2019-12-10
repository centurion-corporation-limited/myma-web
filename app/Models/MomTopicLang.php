<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class MomTopicLang extends Model
{
    protected $connection = "mysql_2";
    protected $table = "mom_topic_locale";
    public $timestamps = false;

    protected $fillable = [
        'title', 'topic_id', 'language', 'content', 'video_id'
    ];

    // public function type()
    // {
    //     return $this->belongsTo(Type::class);
    // }

}
