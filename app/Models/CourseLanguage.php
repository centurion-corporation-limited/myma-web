<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class CourseLanguage extends Model
{
  use Sortable;
    
    protected $connection = "mysql_2";
    protected $table = "course_language";
    public $timestamps = false;
    protected $fillable = [
        'course_id','venue','audience', 'title', 'about', 'description', 'help_text', 'duration_label', 'language'
    ];

}
