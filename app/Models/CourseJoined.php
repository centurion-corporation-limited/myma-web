<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class CourseJoined extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "course_joined";
    public $timestamps = true;
    protected $fillable = [
        'user_id','course_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
