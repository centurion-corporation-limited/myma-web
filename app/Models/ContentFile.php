<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class ContentFile extends Model
{

    protected $connection = "mysql_2";
    protected $table = "course_content_files";
    public $timestamps = false;

    protected $fillable = [
        'content_id', 'path', 'language', 'type', 'file_type', 'video_id'
    ];

}
