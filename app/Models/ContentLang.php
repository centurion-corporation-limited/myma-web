<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class ContentLang extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "course_content_language";
    public $timestamps = false;

    protected $fillable = [
        'content_id', 'path', 'language', 'title'
    ];

}
