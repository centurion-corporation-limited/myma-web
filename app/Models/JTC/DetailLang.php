<?php

namespace App\Models\JTC;

use Illuminate\Database\Eloquent\Model;
use App\User;

class DetailLang extends Model
{
    protected $connection = "mysql_2";
    protected $table = "jtc_topic_locale";
    public $timestamps = false;

    protected $fillable = [
        'title', 'topic_id', 'language', 'content', 'video_id', 'author'
    ];

    // public function type()
    // {
    //     return $this->belongsTo(Type::class);
    // }

}
