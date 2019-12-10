<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Content extends Model
{

    protected $connection = "mysql_2";
    protected $table = "course_content";
    public $timestamps = false;

    protected $fillable = [
        'type', 'order', 'path', 'course_id', 'content_type', 'title'
    ];

}
