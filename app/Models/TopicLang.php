<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class TopicLang extends Model
{
    protected $connection = "mysql_2";
    protected $table = "topic_language";
    public $timestamps = false;

    protected $fillable = [
        'title','description', 'language','topic_id'
    ];

}
